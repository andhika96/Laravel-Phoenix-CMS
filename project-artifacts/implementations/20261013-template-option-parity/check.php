<?php
require dirname(__DIR__, 2).'/mockups/20261012_template-options-ui-revamp/bootstrap.php';
$catalog=app(App\Support\Article\ArticleTemplateCatalog::class);
$normalizer=app(App\Support\Article\ArticleTemplateOptions::class);
$fixture=app(App\Support\Article\ArticleTemplatePreviewFixture::class);
$count=0;
function verify($condition,$message) { global $count; if (!$condition) throw new RuntimeException($message); $count++; }
foreach (['minimal-reading-list','editorial-journal','mosaic-magazine'] as $key) {
 $options=$normalizer->archive($key,['display'=>['category'=>false,'date'=>false,'excerpt'=>false,'read_more'=>false,'reading_time'=>false]]);
 $posts=$fixture->archivePaginator(1,'/article',6);
 $html=view($catalog->view('archive',$key),['templateOptions'=>$options,'articles'=>$posts,'popularArticles'=>collect(),'articleCategories'=>collect(),'isPreviewFixture'=>true])->render();
 verify(!str_contains($html,'class="article-excerpt-clamp"'),$key.' excerpt hidden');
 verify(!str_contains($html,'class="article-chip"'),$key.' category hidden');
 verify(!str_contains($html,'class="article-text-link'),$key.' read link hidden');
}
$classic=$normalizer->archive('mosaic-classic',['mosaic_classic'=>['show_author'=>false,'show_date'=>false,'show_reading_time'=>false]]);
$html=view($catalog->view('archive','mosaic-classic'),['templateOptions'=>$classic,'articles'=>$fixture->archivePaginator(1,'/article',12),'isPreviewFixture'=>true])->render();
verify(!str_contains($html,'class="article-classic-meta"'),'Classic metadata toggles suppress all regions');
foreach(array_keys($catalog->detail()) as $key) {
 $html=view($catalog->view('detail',$key),['templateOptions'=>$normalizer->detail($key,['display'=>['date'=>false,'author'=>false,'navigation'=>false]]),'article'=>$fixture->detailArticle(),'relatedArticles'=>collect(),'previousArticle'=>$fixture->detailArticle(),'nextArticle'=>$fixture->detailArticle(),'isPreviewFixture'=>true])->render();
 verify(!str_contains($html,'class="article-detail__meta"'),$key.' metadata hidden');
 verify(!str_contains($html,'class="article-detail-navigation"'),$key.' navigation hidden');
}
$custom=$normalizer->archive('minimal-reading-list',['pagination'=>['type'=>'boxed','item_border_width'=>'3px','item_radius'=>'8px','frame'=>['enabled'=>true,'border_width'=>'2px']]]);
verify($custom['pagination']['item_border_width']==='3px'&&$custom['pagination']['item_radius']==='8px'&&$custom['pagination']['frame']['enabled'],'Explicit saved overrides preserved');
$source=file_get_contents($root.'/resources/views/manage_article/templates/index.blade.php');preg_match('/<Teleport to="body">.*?<\/Teleport>/s',$source,$match);
file_put_contents(__DIR__.'/modal.blade.php',$match[0]);
$html=view()->file(__DIR__.'/modal.blade.php')->render();
verify(str_contains($html,'Show author / editor')&&str_contains($html,'Show reading time')&&str_contains($html,'Visible elements'),'Modal options compile');
file_put_contents(__DIR__.'/modal.html',$html);
echo "$count runtime Blade checks passed\n";
