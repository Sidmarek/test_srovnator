<?php
// source: C:\xampp\htdocs\srovnator\app\presenters/templates/Overview/show.latte

class Template892da7f68b615d88df7ac6cbb1746e85 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('cbfc613512', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lbd75d675ffc_content')) { function _lbd75d675ffc_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><p>Údaje:<br>
 Vaše PSČ: <?php echo Latte\Runtime\Filters::escapeHtml($udaje->psc, ENT_NOQUOTES) ?><br>
 Váš e-mail: <?php echo Latte\Runtime\Filters::escapeHtml($udaje->email, ENT_NOQUOTES) ?><br>
 Váš věk: <?php echo Latte\Runtime\Filters::escapeHtml($udaje->vek, ENT_NOQUOTES) ?><br>
 Váš typ vozidla: <?php echo Latte\Runtime\Filters::escapeHtml($udaje->vozidlo, ENT_NOQUOTES) ?><br>
 Objem vašeho vozidla:<?php echo Latte\Runtime\Filters::escapeHtml($udaje->objem_vozidla, ENT_NOQUOTES) ?><br>
 Výkon vašeho vozidla: <?php echo Latte\Runtime\Filters::escapeHtml($udaje->vykon_vozidla, ENT_NOQUOTES) ?><br>
 Hmotnost vašeho vozidla:<?php echo Latte\Runtime\Filters::escapeHtml($udaje->hmotnost, ENT_NOQUOTES) ?><br>
 Cena vašeho pojisštení: <?php echo Latte\Runtime\Filters::escapeHtml($cena, ENT_NOQUOTES) ?><br>
 </p>
<?php
}}

//
// end of blocks
//

// template extending

$_l->extends = empty($_g->extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $_g->extended = TRUE;

if ($_l->extends) { ob_start();}

// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIRuntime::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 
}}