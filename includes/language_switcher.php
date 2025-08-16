<?php
/**
 * Language Switcher Component
 * Creates a language selection dropdown for the mytransfers website
 * Supports Turkish (tr), German (de), French (fr), Spanish (es), and English (en)
 */

// Get current language from cookie or default to 'en'
$current_lang = isset($_COOKIE['site_language']) ? $_COOKIE['site_language'] : 'en';

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

<div class="language-switcher">
    <div class="language-dropdown">
        <button class="language-toggle" onclick="toggleLanguageDropdown()" type="button">
            <img src="assets/mytransfersweb/prod/images/<?php echo $languages[$current_lang]['flag']; ?>" 
                 alt="<?php echo $languages[$current_lang]['name']; ?>" 
                 class="flag-icon">
            <span class="language-name"><?php echo $languages[$current_lang]['name']; ?></span>
            <svg class="dropdown-arrow" width="12" height="8" viewBox="0 0 12 8" fill="none">
                <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        
        <div class="language-options" id="languageOptions" style="display: none;">
            <?php foreach ($languages as $code => $info): ?>
                <?php if ($code !== $current_lang): ?>
                    <a href="javascript:void(0)" 
                       class="language-option" 
                       onclick="changeLanguage('<?php echo $code; ?>')"
                       data-lang="<?php echo $code; ?>">
                        <img src="assets/mytransfersweb/prod/images/<?php echo $info['flag']; ?>" 
                             alt="<?php echo $info['name']; ?>" 
                             class="flag-icon">
                        <span><?php echo $info['name']; ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.language-switcher {
    position: relative;
    display: inline-block;
}

.language-dropdown {
    position: relative;
}

.language-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: #ffffff;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.language-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
}

.flag-icon {
    width: 20px;
    height: 15px;
    object-fit: cover;
    border-radius: 2px;
}

.language-name {
    font-size: 14px;
    white-space: nowrap;
}

.dropdown-arrow {
    transition: transform 0.3s ease;
}

.language-toggle.active .dropdown-arrow {
    transform: rotate(180deg);
}

.language-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid #e1e5e9;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    margin-top: 4px;
    overflow: hidden;
}

.language-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    color: #333333;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
}

.language-option:last-child {
    border-bottom: none;
}

.language-option:hover {
    background-color: #f8f9fa;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .language-name {
        display: none;
    }
    
    .language-toggle {
        padding: 8px;
    }
}
</style>

<script>
function toggleLanguageDropdown() {
    const options = document.getElementById('languageOptions');
    const toggle = document.querySelector('.language-toggle');
    
    if (options.style.display === 'none' || options.style.display === '') {
        options.style.display = 'block';
        toggle.classList.add('active');
    } else {
        options.style.display = 'none';
        toggle.classList.remove('active');
    }
}

function changeLanguage(langCode) {
    // Set cookie for 1 year
    const expires = new Date();
    expires.setFullYear(expires.getFullYear() + 1);
    document.cookie = `site_language=${langCode}; expires=${expires.toUTCString()}; path=/`;
    
    // Reload page to apply new language
    window.location.reload();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const switcher = document.querySelector('.language-switcher');
    const options = document.getElementById('languageOptions');
    const toggle = document.querySelector('.language-toggle');
    
    if (!switcher.contains(event.target)) {
        options.style.display = 'none';
        toggle.classList.remove('active');
    }
});
</script>
