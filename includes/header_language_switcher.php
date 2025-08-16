<?php
/**
 * Header Language Switcher Component
 * Integrated language selector for the navigation header
 */

// Get current language from the global language loader
global $lang_loader;
$current_lang = $lang_loader->getCurrentLanguage();

// Available languages with their display names and flag icons
$languages = [
    'en' => ['name' => 'English', 'flag' => 'en.svg'],
    'tr' => ['name' => 'Türkçe', 'flag' => 'tr.svg'], 
    'de' => ['name' => 'Deutsch', 'flag' => 'de.svg'],
    'fr' => ['name' => 'Français', 'flag' => 'fr.svg'],
    'es' => ['name' => 'Español', 'flag' => 'es.svg']
];

// Make sure current language exists in our list
if (!array_key_exists($current_lang, $languages)) {
    $current_lang = 'en';
}
?>

<div class="position-relative dropdown c-center">
    <a class="nav-item nav-link px-4 dropdown-toggle" href="#"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <span class="icon-flags">
            <img class="lazy" width="30" height="14"
                data-src="/mytransfers/assets/mytransfersweb/prod/images/<?php echo $languages[$current_lang]['flag']; ?>"
                data-srcset="/mytransfers/assets/mytransfersweb/prod/images/<?php echo $languages[$current_lang]['flag']; ?> 1x"
                alt="<?php echo $current_lang; ?>" title="<?php echo $current_lang; ?>">
        </span>
    </a>
    <div class="dropdown-menu dm-lenguage" aria-labelledby="dropdownMenuButton">
        <div class="dropdown-item" href="#">
            <?php foreach ($languages as $code => $info): ?>
                <a class="col-12 pt-1 pb-2 px-3 link-hover link-ln"
                   href="<?php echo $lang_loader->getUrlForLanguage($code); ?>"
                   <?php if ($code === $current_lang): ?>style="font-weight: bold;"<?php endif; ?>>
                    <span class="icon-flags">
                        <img class="lazy" width="30" height="20"
                            data-src="/mytransfers/assets/mytransfersweb/prod/images/<?php echo $info['flag']; ?>"
                            data-srcset="/mytransfers/assets/mytransfersweb/prod/images/<?php echo $info['flag']; ?> 1x"
                            alt="<?php echo $code; ?>"
                            title="<?php echo $info['name']; ?>" />
                    </span>
                    <span> <?php echo $info['name']; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Language switching now handled by direct URL navigation -->
