<!--
Localized newsletter widget.
Currently only german (for all locales starting with 'de' and English for everyone else

The className variable is used to pass any class that we may want the iframe to have.
-->
<?php if(!isset($className)) $className = ""; ?>

@if (substr( App::getLocale(), 0, 2) === "de")
	<iframe class="{{ $className }}" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://widget.mailjet.com/f7e724392587055432c8449c203368bec4ade1b3.html" width="262" height="200"></iframe>
@else
	<iframe class="{{ $className }}" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://widget.mailjet.com/3ae1768ecf1767f9dfaa9f5c7fc3864cc0252b69.html" width="262" height="200"></iframe>
@endif
