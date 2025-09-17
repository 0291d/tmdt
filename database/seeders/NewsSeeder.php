<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\Image;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $files = [
            [
                'file' => base_path('VisualStudioCode/news1.html'),
                'fallback_title' => 'News 1',
                'image_src' => 'img/imgnew1.webp',
                'image_dest' => 'news/news1.webp',
            ],
            [
                'file' => base_path('VisualStudioCode/news2.html'),
                'fallback_title' => 'News 2',   
                'image_src' => 'img/news21.webp',
                'image_dest' => 'news/news2.webp',
            ],
            [
                'file' => base_path('VisualStudioCode/news3.html'),
                'fallback_title' => 'News 3',
                'image_src' => 'img/news31.webp',
                'image_dest' => 'news/news3.webp',
            ],
        ];
        // Remove old summarized seed entries if they exist
        $oldTitles = [
            'Cẩm nang phòng khách: 5 cách xử lý mùi hôi cho sofa da',
            'Phòng khách thoải mái hơn nếu tránh các sai lầm khi trang trí',
            'Nội thất Bắc Âu: khi cảm xúc và sáng tạo lên ngôi',
        ];
        $oldNews = News::whereIn('title', $oldTitles)->get();
        foreach ($oldNews as $n) {
            Image::where('imageable_type', News::class)->where('imageable_id', $n->getKey())->delete();
            $n->delete();
        }

        foreach ($files as $fIndex => $f) {
            $html = is_file($f['file']) ? @file_get_contents($f['file']) : null;
            $title = $f['fallback_title'];
            $contentHtml = null;

            if ($html) {
                // Parse HTML and extract exact title and main-widget content
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadHTML($html);
                libxml_clear_errors();
                $xpath = new \DOMXPath($dom);

                $titleNode = $xpath->query("//div[contains(@class,'title-widget')]//h1")->item(0);
                if ($titleNode) {
                    $title = trim($titleNode->textContent);
                }

                $mainNode = $xpath->query("//div[contains(@class,'main-widget')]")->item(0);
                if ($mainNode) {
                    // Remove inline images from content; only keep text and markup
                    $imgNodes = (new \DOMXPath($dom))->query('.//img', $mainNode) ?: [];
                    foreach ($imgNodes as $img) {
                        $img->parentNode?->removeChild($img);
                    }
                    $contentHtml = '';
                    foreach ($mainNode->childNodes as $child) {
                        $contentHtml .= $dom->saveHTML($child);
                    }
                }
            }

            $news = News::updateOrCreate(
                ['title' => $title],
                ['content' => $contentHtml ?? '']
            );

            // Attach main image if not exists
            $hasMain = $news->images()->where('is_main', true)->exists();
            if (!$hasMain) {
                $srcPath = public_path($f['image_src']);
                $dest = $f['image_dest'];
                if (is_file($srcPath)) {
                    $bytes = @file_get_contents($srcPath);
                    if ($bytes !== false) {
                        Storage::disk('public')->put($dest, $bytes);
                        Image::create([
                            'imageable_id' => (string) $news->getKey(),
                            'imageable_type' => News::class,
                            'path' => $dest,
                            'is_main' => true,
                        ]);
                    }
                }
            }

            // Ensure only main image remains (remove any non-main images for these seeded entries)
            $news->images()->where('is_main', false)->delete();
        }
    }
}
