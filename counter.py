#! /usr/bin/env python

import sys, os.path, socket, math, re
import ConfigParser
from datetime import tzinfo, timedelta, datetime
# import vlastnich knihoven
from lib import MC1C, fileWriter, dbWriter

class UTC(tzinfo):
    """UTC"""
    def utcoffset(self, dt):
        return timedelta(0)
    def tzname(self, dt):
        return "UTC"
    def dst(self, dt):
        return timedelta(0)

# Otestovani jestli mame k dispozici konfigurak
if (len(sys.argv) != 2):
    print "usage:", sys.argv[0], "config.file"
    print "  where: config.file is configuration file for given counter"
    sys.exit(2)

configFile = sys.argv[1]

# Otestovani existence konfiguracniho souboru
if not (os.path.exists(configFile)):
    print "Configuration file", configFile, "does not exists"
    sys.exit(1)

# Nacteni konfiguracniho souboru
config = ConfigParser.RawConfigParser()
config.read(configFile)

try:
    counterIp = config.get('COUNTER', 'ip')
    counterPort = config.getint('COUNTER', 'port')
    counterBreakHour = config.get('COUNTER', 'breakHour')
    counterVersion = config.get('COUNTER', 'version')
    morosPort = config.getint('MOROS', 'port')
    
    # kam budu ukladat
    # typ vystupu a kontrola podporovanych
    output = config.get('OUTPUT', 'type')
    outputType = [txt.strip() for txt in output.split(',')]
    if not ('file' in outputType or 'db' in outputType):
        print "Only Unsupported output types:", outputType, "; Supported types: file, db"
    
    # Zlom vyroby - hodina, minuta
    counterBreak = re.search("^([0-9]{1,2}):?([0-9]{1,2})?$", counterBreakHour)
    if (counterBreak):
        breakHour = int(counterBreak.group(1))
        breakMinute = int(counterBreak.group(2)) if counterBreak.group(2) is not None else 0
    else:
        print "Illegal break hour[:minute] value"
        sys.exit(1)

    # kontrola podporovane verze pocitacky
    if not (counterVersion == 'v7' or counterVersion == 'v8'):
        print "Unsupported counter version:", counterVersion, "; Supported values: v7, v8"


#    print "break:", str(breakHour) + ":" + str(breakMinute)
except (ConfigParser.NoOptionError, ConfigParser.NoSectionError):
    print "Illegal configuration file:", configFile
    sys.exit(1)

counterAddr = (counterIp, counterPort)


# Kolik je hodin?
inception = datetime(2000, 1, 1, 0, 0, 0, 0, UTC())
now = datetime.now()
unow = datetime.now(UTC())
tds = unow-inception
pktime = int(((tds.seconds + tds.days * 24 * 3600) * 10**6) / 10**6)

# Wytvoreni writeru a nacteni konfigurace
#writer = {
#    'db': DbWriter.DbWriter(now)
#}.get(outputType, FileWriter.FileWriter(now))

#writer.loadConfig(config)

writers = []
if 'file' in outputType:
    writers.append(fileWriter.FileWriter(now))
if 'db' in outputType:
    writers.append(dbWriter.DbWriter(now))

try:
    for writer in writers:
        writer.loadConfig(config)

except (ConfigParser.NoOptionError, ConfigParser.NoSectionError):
    print "Illegal configuration file:", configFile
    sys.exit(1)


################################
# --- Sekce komunikace dat --- #

v8 = [MC1C.WR("D02201E", MC1C.D02201EResponseParser()), MC1C.WR("D01303C", MC1C.D01303CResponseParser()), MC1C.WR("D01901E", MC1C.D01901EResponseParser())]
v7 = [MC1C.WR("D020014", MC1C.D020014ResponseParser()), MC1C.WR("D01303C", MC1C.D01303CResponseParser())]

# Vytvoreni soketu, poslouchani na danem portu
s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
s.bind(('', morosPort))
s.settimeout(4)

try:
    msgId = 0
    for msg in v8 if counterVersion == 'v8' else v7:
        s.sendto(msg.getRequest(), counterAddr)
        data = s.recv(1024)
        msgId += 1
        msg.setResponse(data)
except socket.timeout:
    print "Response timeout"
    sys.exit(1)
finally: 
    s.close()

# prace se ziskanymi daty - projiti, vytvoreni objektu a ulozeni dle specifikace

data = {"pktime": pktime, "boxesAssortment": {}, "boxpiecesAssortment": {}}

for msg in v8 if counterVersion == 'v8' else v7:
    # test, jestli neni chybova hlaska v navratove hodnote
    if (msg.getResponse()[0] == '\x15'):
        print "Response error"
        sys.exit(1)

    # Mohu parsovat odpoved do datoveho objektu
    msg.parseResponse(data)


# Pokud jsem se dostal sem, mam vsechna data a mohu je ulozit
# - v opacnem pripade zustavji v souboru (pokud existuje a je nastaveno souborove ukladani) stara data

# Zapis dat dle pozadovanych (a dostupnych) zapisovacu

for writer in writers:
    writer.write(data)  
