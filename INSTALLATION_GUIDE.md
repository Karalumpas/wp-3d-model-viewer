# WP 3D Model Viewer - Installation Guide

## Løsning på "Core plugin file is missing" fejl

Hvis du får fejlen: **"WP 3D Model Viewer Error: Core plugin file is missing. Please reinstall the plugin."** 

Følg denne step-by-step guide:

## 🔧 Løsning

### Step 1: Tjek Plugin Mappe Navn
Plugin mappen SKAL hedde præcis: `wp-3d-model-viewer`

❌ **Forkert:** `wp-3d-model-viewer-v2.0.0-fixed-1`  
❌ **Forkert:** `wp-3d-model-viewer-fixed`  
✅ **Korrekt:** `wp-3d-model-viewer`

### Step 2: Upload Alle Filer
Sikr dig at ALLE filer er uploaded til: `/wp-content/plugins/wp-3d-model-viewer/`

**Påkrævede fil struktur:**
```
wp-content/plugins/wp-3d-model-viewer/
├── wp-3d-model-viewer.php          ← Hovedfil
├── uninstall.php
├── index.php
├── admin/
│   ├── class-wp-3d-model-viewer-admin.php
│   ├── css/
│   ├── js/
│   └── partials/
├── includes/
│   ├── class-wp-3d-model-viewer.php       ← VIGTIG!
│   ├── class-wp-3d-model-viewer-activator.php
│   ├── class-wp-3d-model-viewer-deactivator.php
│   ├── class-wp-3d-model-viewer-loader.php
│   ├── class-wp-3d-model-viewer-i18n.php
│   └── class-wp-3d-model-viewer-cpt.php
├── public/
│   └── class-wp-3d-model-viewer-public.php
└── languages/
```

### Step 3: Test Installation
1. Upload `installation-test.php` til plugin mappen
2. Besøg: `http://dit-website.dk/wp-content/plugins/wp-3d-model-viewer/installation-test.php`
3. Tjek om alle filer er til stede
4. Slet `installation-test.php` før aktivering

### Step 4: Filrettigheder
Sæt korrekte filrettigheder:
```bash
chmod 755 /wp-content/plugins/wp-3d-model-viewer/
chmod 644 /wp-content/plugins/wp-3d-model-viewer/*.php
chmod 755 /wp-content/plugins/wp-3d-model-viewer/*/
```

### Step 5: Aktivér Plugin
1. Gå til WordPress Admin → Plugins
2. Find "WP 3D Model Viewer"
3. Klik "Activate"

## 🚨 Almindelige Problemer

### Problem 1: Ufuldstændig Upload
**Symptom:** Nogle filer mangler
**Løsning:** 
- Slet hele plugin mappen
- Upload alle filer igen fra zip-filen

### Problem 2: Forkert Mappe Navn  
**Symptom:** Plugin findes ikke eller kan ikke aktiveres
**Løsning:**
- Omdøb mappen til præcis `wp-3d-model-viewer`

### Problem 3: Filrettigheder
**Symptom:** "Permission denied" eller "Cannot read file"
**Løsning:**
- Kontakt din hosting udbyder
- Sæt korrekte filrettigheder (se Step 4)

### Problem 4: Korrupte Filer
**Symptom:** Plugin fejler under aktivering
**Løsning:**
- Download ny kopi af plugin
- Upload igen

## 📋 Tjekliste Før Aktivering

- [ ] Plugin mappe hedder `wp-3d-model-viewer`
- [ ] Alle filer fra zip er uploaded
- [ ] `includes/class-wp-3d-model-viewer.php` findes
- [ ] Filrettigheder er sat korrekt
- [ ] Test fil er slettet
- [ ] Ingen andre versioner af plugin er installeret

## 🔍 Debug Information

Hvis problemet fortsætter, vil plugin'et nu vise mere detaljeret fejlinformation:
- Hvilken fil der mangler
- Sti til plugin mappen  
- Forslag til løsning

## 📞 Support

Hvis du stadig har problemer:
1. Kør installation test filen
2. Tjek din server's error log
3. Kontakt din hosting udbyder for hjælp med filrettigheder
