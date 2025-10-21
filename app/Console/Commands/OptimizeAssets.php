<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OptimizeAssets extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'assets:optimize {--force : Forcer la ré-optimisation}';

    /**
     * The description of the console command.
     */
    protected $description = 'Optimiser les ressources statiques (CSS, JS, Images)';

    private $stats = [
        'css_files' => 0,
        'js_files' => 0,
        'images' => 0,
        'original_size' => 0,
        'optimized_size' => 0
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Optimisation des ressources statiques...');
        $this->newLine();

        // 1. Optimiser les fichiers CSS
        $this->optimizeCSS();

        // 2. Optimiser les fichiers JavaScript
        $this->optimizeJS();

        // 3. Optimiser les images
        $this->optimizeImages();

        // 4. Créer le fichier de version pour le cache busting
        $this->generateVersionFile();

        // 5. Afficher les statistiques
        $this->displayStats();

        $this->newLine();
        $this->info('✅ Optimisation terminée !');
    }

    private function optimizeCSS()
    {
        $this->info('🎨 Optimisation des fichiers CSS...');

        $cssPath = public_path('css');
        if (!File::exists($cssPath)) {
            $this->warn('Dossier CSS non trouvé : ' . $cssPath);
            return;
        }

        $cssFiles = File::files($cssPath);
        
        foreach ($cssFiles as $file) {
            if ($file->getExtension() === 'css' && !str_contains($file->getBasename(), '.min.')) {
                $this->optimizeCSSFile($file->getPathname());
            }
        }

        $this->info('✅ CSS optimisé - ' . $this->stats['css_files'] . ' fichiers traités');
    }

    private function optimizeCSSFile($filePath)
    {
        $originalContent = File::get($filePath);
        $originalSize = strlen($originalContent);

        // Optimisations CSS basiques
        $optimizedContent = $originalContent;
        
        // Supprimer les commentaires CSS
        $optimizedContent = preg_replace('/\/\*.*?\*\//s', '', $optimizedContent);
        
        // Supprimer les espaces et sauts de ligne inutiles
        $optimizedContent = preg_replace('/\s+/', ' ', $optimizedContent);
        $optimizedContent = str_replace(['; ', ' {', '{ ', ' }', '} ', ': '], [';', '{', '{', '}', '}', ':'], $optimizedContent);
        
        // Supprimer les espaces autour des virgules et points-virgules
        $optimizedContent = str_replace([', ', ' ,', ' ;'], [',', ',', ';'], $optimizedContent);
        
        $optimizedSize = strlen($optimizedContent);

        // Sauvegarder la version optimisée
        $minFilePath = str_replace('.css', '.min.css', $filePath);
        File::put($minFilePath, trim($optimizedContent));

        $this->stats['css_files']++;
        $this->stats['original_size'] += $originalSize;
        $this->stats['optimized_size'] += $optimizedSize;

        $this->line('  📄 ' . basename($filePath) . ' -> ' . basename($minFilePath) . ' (' . $this->formatSize($originalSize - $optimizedSize) . ' économisés)');
    }

    private function optimizeJS()
    {
        $this->info('⚡ Optimisation des fichiers JavaScript...');

        $jsPath = public_path('js');
        if (!File::exists($jsPath)) {
            $this->warn('Dossier JS non trouvé : ' . $jsPath);
            return;
        }

        $jsFiles = File::files($jsPath);
        
        foreach ($jsFiles as $file) {
            if ($file->getExtension() === 'js' && !str_contains($file->getBasename(), '.min.')) {
                $this->optimizeJSFile($file->getPathname());
            }
        }

        $this->info('✅ JavaScript optimisé - ' . $this->stats['js_files'] . ' fichiers traités');
    }

    private function optimizeJSFile($filePath)
    {
        $originalContent = File::get($filePath);
        $originalSize = strlen($originalContent);

        // Optimisations JavaScript basiques
        $optimizedContent = $originalContent;
        
        // Supprimer les commentaires JavaScript (lignes simples)
        $optimizedContent = preg_replace('/\/\/.*$/m', '', $optimizedContent);
        
        // Supprimer les commentaires multi-lignes
        $optimizedContent = preg_replace('/\/\*.*?\*\//s', '', $optimizedContent);
        
        // Supprimer les espaces et sauts de ligne inutiles
        $optimizedContent = preg_replace('/\s+/', ' ', $optimizedContent);
        
        // Optimisations spécifiques JavaScript
        $optimizedContent = str_replace(['; ', ' {', '{ ', ' }', '} '], [';', '{', '{', '}', '}'], $optimizedContent);
        
        $optimizedSize = strlen($optimizedContent);

        // Sauvegarder la version optimisée
        $minFilePath = str_replace('.js', '.min.js', $filePath);
        File::put($minFilePath, trim($optimizedContent));

        $this->stats['js_files']++;
        $this->stats['original_size'] += $originalSize;
        $this->stats['optimized_size'] += $optimizedSize;

        $this->line('  📄 ' . basename($filePath) . ' -> ' . basename($minFilePath) . ' (' . $this->formatSize($originalSize - $optimizedSize) . ' économisés)');
    }

    private function optimizeImages()
    {
        $this->info('🖼️  Optimisation des images...');

        $imagesPath = public_path('images');
        if (!File::exists($imagesPath)) {
            $this->warn('Dossier images non trouvé : ' . $imagesPath);
            return;
        }

        $imageFiles = File::allFiles($imagesPath);
        
        foreach ($imageFiles as $file) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $this->optimizeImageFile($file->getPathname());
            }
        }

        $this->info('✅ Images optimisées - ' . $this->stats['images'] . ' fichiers traités');
    }

    private function optimizeImageFile($filePath)
    {
        $originalSize = filesize($filePath);
        $optimized = false;

        // Optimisation basique : copier le fichier avec compression
        try {
            $imageInfo = getimagesize($filePath);
            if ($imageInfo) {
                $mime = $imageInfo['mime'];
                
                switch ($mime) {
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($filePath);
                        if ($image) {
                            // Créer une version optimisée
                            $optimizedPath = str_replace('.jpg', '_optimized.jpg', $filePath);
                            $optimizedPath = str_replace('.jpeg', '_optimized.jpeg', $optimizedPath);
                            imagejpeg($image, $optimizedPath, 85); // Qualité 85%
                            imagedestroy($image);
                            $optimized = true;
                        }
                        break;
                        
                    case 'image/png':
                        $image = imagecreatefrompng($filePath);
                        if ($image) {
                            $optimizedPath = str_replace('.png', '_optimized.png', $filePath);
                            imagepng($image, $optimizedPath, 6); // Compression niveau 6
                            imagedestroy($image);
                            $optimized = true;
                        }
                        break;
                }
                
                if ($optimized && file_exists($optimizedPath)) {
                    $optimizedSize = filesize($optimizedPath);
                    $this->stats['images']++;
                    $this->stats['original_size'] += $originalSize;
                    $this->stats['optimized_size'] += $optimizedSize;
                    
                    $this->line('  🖼️  ' . basename($filePath) . ' -> ' . basename($optimizedPath) . ' (' . $this->formatSize($originalSize - $optimizedSize) . ' économisés)');
                }
            }
        } catch (\Exception $e) {
            $this->line('  ⚠️  Erreur lors de l\'optimisation de ' . basename($filePath) . ': ' . $e->getMessage());
        }
    }

    private function generateVersionFile()
    {
        $this->info('🔢 Génération du fichier de version...');

        $version = time(); // Utiliser le timestamp comme version
        $versionFile = public_path('version.json');

        $versionData = [
            'version' => $version,
            'generated_at' => now()->toISOString(),
            'stats' => $this->stats
        ];

        File::put($versionFile, json_encode($versionData, JSON_PRETTY_PRINT));
        $this->info('✅ Fichier de version créé : version.json');
    }

    private function displayStats()
    {
        $this->newLine();
        $this->info('📊 Statistiques d\'optimisation :');
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Fichiers CSS traités', $this->stats['css_files']],
                ['Fichiers JS traités', $this->stats['js_files']],
                ['Images traitées', $this->stats['images']],
                ['Taille originale totale', $this->formatSize($this->stats['original_size'])],
                ['Taille optimisée totale', $this->formatSize($this->stats['optimized_size'])],
                ['Économie totale', $this->formatSize($this->stats['original_size'] - $this->stats['optimized_size'])],
                ['Réduction', $this->getReductionPercentage() . '%']
            ]
        );
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function getReductionPercentage()
    {
        if ($this->stats['original_size'] == 0) {
            return 0;
        }
        
        $reduction = (($this->stats['original_size'] - $this->stats['optimized_size']) / $this->stats['original_size']) * 100;
        return round($reduction, 1);
    }
}