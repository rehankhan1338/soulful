<?php
/**
 * Builds the Soulful Beginnings Academy page as NATIVE Elementor containers/widgets.
 * Run with: wp eval-file build.php
 */

if (!defined('ABSPATH')) { fwrite(STDERR, "Run via wp eval-file\n"); exit(1); }

global $map, $GID;
$raw = file_get_contents(ABSPATH . 'asset-map.json');
$raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw); // strip UTF-8 BOM
$map = json_decode($raw, true);
if (!$map) { echo "asset-map.json missing/empty\n"; exit(1); }

function A($name){ global $map; return $map[$name] ?? ['id'=>0,'url'=>'']; }
function url($name){ return A($name)['url']; }
function img($name,$alt=''){ $a=A($name); return ['url'=>$a['url'],'id'=>$a['id'],'alt'=>$alt,'source'=>'library','size'=>'']; }

$GID = 0;
function nid(){ global $GID; $GID++; return substr(md5('sba'.$GID),0,7); }

/* ---------- element builders ---------- */
function con($cls, $children, $set=[], $inner=true){
  return [
    'id'=>nid(),'elType'=>'container','isInner'=>$inner,
    'settings'=>array_merge(['content_width'=>'full','_css_classes'=>$cls,'css_classes'=>$cls], $set),
    'elements'=>$children,
  ];
}
function heading($text,$cls='',$tag='h2'){
  return ['id'=>nid(),'elType'=>'widget','widgetType'=>'heading','elements'=>[],
    'settings'=>['title'=>$text,'header_size'=>$tag,'_css_classes'=>$cls]];
}
function text($html,$cls=''){
  return ['id'=>nid(),'elType'=>'widget','widgetType'=>'text-editor','elements'=>[],
    'settings'=>['editor'=>$html,'_css_classes'=>$cls]];
}
function image_w($name,$cls='',$alt=''){
  return ['id'=>nid(),'elType'=>'widget','widgetType'=>'image','elements'=>[],
    'settings'=>['image'=>img($name,$alt),'image_size'=>'full','_css_classes'=>$cls]];
}
function button($t,$link,$cls=''){
  return ['id'=>nid(),'elType'=>'widget','widgetType'=>'button','elements'=>[],
    'settings'=>['text'=>$t,'link'=>['url'=>$link,'is_external'=>'','nofollow'=>''],'_css_classes'=>$cls]];
}
function html_w($html,$cls=''){
  return ['id'=>nid(),'elType'=>'widget','widgetType'=>'html','elements'=>[],
    'settings'=>['html'=>$html,'_css_classes'=>$cls]];
}
/* Dynamic navigation menu (Header Footer Elementor's free "navigation-menu"
   widget), bound to the WordPress "Primary Menu". Editable in Appearance > Menus;
   styled via the widget + sba-design.css. Includes a responsive hamburger. */
function navmenu_w($cls='', $dropdown='mobile'){
  return ['id'=>nid(),'elType'=>'widget','widgetType'=>'navigation-menu','elements'=>[],
    'settings'=>[
      'menu'=>'primary-menu',
      'layout'=>'horizontal',
      'submenu_icon'=>'none',
      'dropdown'=>$dropdown,   // 'mobile' = hamburger on phones (header); 'none' = always inline (footer)
      'align_items'=>'center',
      '_css_classes'=>$cls,
    ]];
}
/* Native Elementor Video widget: YouTube + custom poster overlay + click-to-open lightbox.
   Recreates the original popup player without an HTML widget or extra plugin. */
function video_w($youtube,$posterUrl,$posterId,$cls=''){
  return ['id'=>nid(),'elType'=>'widget','widgetType'=>'video','elements'=>[],
    'settings'=>[
      'video_type'=>'youtube',
      'youtube_url'=>$youtube,
      'aspect_ratio'=>'169',
      'show_image_overlay'=>'yes',
      'image_overlay'=>['url'=>$posterUrl,'id'=>$posterId,'source'=>'library','size'=>''],
      'lightbox'=>'yes',
      'play_icon_type'=>'default',
      '_css_classes'=>$cls,
    ]];
}

/* shared svg snippets */
$icoFb='<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12Z"/></svg>';
$icoLi='<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 5a2 2 0 1 1-4-.002 2 2 0 0 1 4 .002ZM7 8.48H3V21h4V8.48Zm6.32 0H9.34V21h3.94v-6.57c0-3.66 4.77-4 4.77 0V21H22v-7.93c0-6.17-7.06-5.94-8.72-2.91l.04-1.68Z"/></svg>';
$icoIg='<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16Zm0 4.86a4.98 4.98 0 1 0 0 9.96 4.98 4.98 0 0 0 0-9.96Zm0 8.22a3.24 3.24 0 1 1 0-6.48 3.24 3.24 0 0 1 0 6.48Zm6.34-8.42a1.16 1.16 0 1 1-2.32 0 1.16 1.16 0 0 1 2.32 0Z"/></svg>';
$icoX='<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.65l-5.21-6.82-5.97 6.82H1.68l7.73-8.84L1.25 2.25h6.82l4.71 6.23 5.46-6.23Zm-1.16 17.52h1.83L7.01 4.13H5.04l12.04 15.64Z"/></svg>';
$icoPhone='<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24 11.5 11.5 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11.5 11.5 0 0 0 .57 3.6 1 1 0 0 1-.25 1.02l-2.2 2.17Z"/></svg>';
$star='<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
$stars5 = '<span class="sb-stars">'.str_repeat($star,5).'</span>';

$logo = url('8def6d18e0d025eb7dd81183c2d6f0f677e1fc76.png');
$pin  = url('15340d355cff33db86c618f294d97c4721f8086f.svg');
$aboutEllipse   = url('about-ellipse.svg');
$missionEllipse = url('mission-ellipse.svg');

/* navigation links (shared) */
$navLinks = '<a href="#">Home</a><a href="#about">About Us</a><a href="#mission">The Vision</a><a href="#services">Test Prep</a><a href="#services">Tutoring</a><a href="#services">Training</a><a href="#">Blog</a>';

$sections = [];

/* ===== 1. TOP BAR ===== */
$sections[] = con('sb-topbar', [
  con('sb-inner', [
    html_w('<div class="sb-topbar__row"><a href="tel:8642385679">'.$icoPhone.'<span>864-238-5679</span></a><span class="sb-social"><a href="#" aria-label="Facebook">'.$icoFb.'</a><a href="#" aria-label="LinkedIn">'.$icoLi.'</a><a href="#" aria-label="Instagram">'.$icoIg.'</a><a href="#" aria-label="X">'.$icoX.'</a></span></div>'),
  ]),
], ['background_background'=>'classic','background_color'=>'#ff0214'], false);

/* ===== 2. HEADER ===== */
$sections[] = con('sb-header', [
  con('sb-inner', [
    image_w('8def6d18e0d025eb7dd81183c2d6f0f677e1fc76.png','sb-logo','Soulful Beginnings Academy'),
    navmenu_w('sb-nav','none'),   // plain inline links; our own hamburger handles mobile
    html_w('<button class="sb-burger" type="button" aria-label="Toggle menu" aria-expanded="false"><span></span><span></span><span></span></button>','sb-burger-w'),
    button('Contact Us','#contact','sb-btn sb-btn--sm'),
  ]),
], ['background_background'=>'classic','background_color'=>'#ffffff'], false);

/* ===== 3. HERO ===== */
$sections[] = con('sb-hero', [
  image_w('hero-blob.png','sb-hero__blob'),
  image_w('hero-hashtag.png','sb-hero__hashtag'),
  con('sb-inner', [
    con('sb-hero__copy', [
      heading('Where Greenville Students x <em>Grow</em> And Succeed','sb-display','h1'),
      image_w('29aeaab9cf93b6fdd06aae42f25bd1b8687015ca.svg','sb-hero__underline'),
      text('<p>Holistic tutoring, test prep, and enrichment programs designed to help students build confidence, master literacy, and succeed academically.</p>','sb-lead'),
      button('Get Started Today','#contact','sb-btn'),
      image_w('hero-arrow.svg','sb-hero__arrow'),
    ]),
    con('sb-hero__media', [
      image_w('76995da3402da90bb040b3370924cf7c01741408.png','sb-hero__people','Smiling students holding books'),
    ]),
  ]),
  image_w('fa8e9cb7c3b3b9f3fec5fc44f9a612b0aacf517c.png','sb-hero__cloud'),
], ['background_background'=>'classic','background_color'=>'#ffd768'], false);

/* ===== 4. ABOUT ===== */
$sections[] = con('sb-about', [
  con('sb-inner', [
    con('sb-media', [
      image_w('d2883124fe313bf136c27598843706cb34138173.png','sb-photo','Tutor working with a happy student'),
      image_w('073b38d978bc5477128564cce9e98f91c2b98a52.png','sb-dots sb-dots--atop'),
      image_w('073b38d978bc5477128564cce9e98f91c2b98a52.png','sb-dots sb-dots--aleft'),
    ]),
    con('sb-about__copy', [
      heading('About <span class="sb-circled">Soulful<img class="sb-circle-mark" src="'.$aboutEllipse.'" alt=""></span> Beginnings','sb-display','h2'),
      text('<p>Since 2010, Soulful Beginnings Academy has provided holistic, student-centered tutoring and enrichment for families across Greenville and the Upstate. Our mission is simple: help every child build confidence, master literacy, and develop the skills they need to thrive in school and in life.</p>'),
      text('<p>Our experienced educators specialize in personalized instruction — one-on-one and small-group tutoring, standardized test prep, summer enrichment camps, and teacher training. Whether your child needs a little extra support or a lot of encouragement, we deliver caring, effective teaching every step of the way.</p>'),
      button('Learn More About Us','#contact','sb-btn'),
    ]),
  ]),
], ['background_background'=>'classic','background_color'=>'#ffffff'], false);

/* ===== 5. STATS (overlaps onto Services below) ===== */
$sections[] = con('sb-stats', [
  con('sb-statbar', [
    con('sb-stat',[ heading('15+','','div'), text('<p>Years of Experience (since 2010)</p>') ]),
    con('sb-stat',[ heading('200+','','div'), text('<p>Students Tutored</p>') ]),
    con('sb-stat',[ heading('K-12','','div'), text('<p>Academic Programs</p>') ]),
    con('sb-stat',[ heading('7','','div'), text('<p>Offered Subjects</p>') ]),
  ]),
], ['background_background'=>'classic','background_color'=>'#ffffff'], false);

/* ===== 6. SERVICES ===== */
$card = function($imgName,$alt,$title,$body){
  return con('sb-card', [
    con('sb-card__img', [ image_w($imgName,'',$alt) ]),
    heading($title,'sb-card__title','h3'),
    text('<p>'.$body.'</p>'),
    button('Learn More','#contact','sb-btn'),
  ]);
};
$sections[] = con('sb-services', [
  html_w('<div class="sb-strip sb-strip--top"></div>'),
  con('sb-inner', [
    heading('Our Educational Services in Greenville, SC','sb-section-title','h2'),
    con('sb-cards', [
      $card('8a7da4b92b2327a7facfe46247a42d60fe49ca0f.png','One-on-one tutoring session','Personalized K–12 Tutoring Programs','We provide one-on-one and small group tutoring tailored to each student&rsquo;s strengths and challenges.'),
      $card('52ed40147e67a3176b40bc1741408e6853b6211c.png','Students preparing for tests','Standardized Test Prep &amp; Academic Readiness','Prepare your child for success with targeted test prep for MAP, CogAT, ITBS, and state assessments.'),
      $card('2c0d02e6a9fed72360cd345a6b295f7d5e4abf7f.png','Summer camp activities','Educational Summer Camps in Greenville, SC','Our interactive summer camps combine learning with fun through hands-on activities, music, and movement.'),
      $card('32ce3877ad3e670bace0e337e4da1dd527860acd.png','Teacher training workshop','Teacher Training &amp; Educational Workshops','We offer professional development and training sessions for educators focused on literacy instruction, student engagement, and effective teaching strategies.'),
    ]),
  ]),
  html_w('<div class="sb-strip sb-strip--bottom"></div>'),
], [
  'background_background'=>'classic','background_color'=>'#ffd668',
  'background_image'=>img('d36ad0b2bbc8ced3fc6a3677be30a410721d4991.png'),
  'background_size'=>'cover','background_position'=>'center top',
], false);

/* ===== 7. MISSION ===== */
$sections[] = con('sb-mission', [
  con('sb-inner', [
    con('sb-mission__copy', [
      heading('Our Mission: Building <span class="sb-circled">Confident<img class="sb-circle-mark" src="'.$missionEllipse.'" alt=""></span>, Lifelong Learners','sb-display','h2'),
      text('<p>We empower students to believe in themselves and develop the skills they need to succeed in school and beyond. Our goal is to improve reading and comprehension across all subjects, helping students think like mathematicians, scientists, and critical thinkers.</p>'),
      button('Make a Donation','#contact','sb-btn'),
    ]),
    con('sb-media', [
      image_w('50f26401a1287a9e1232f63d35f9ebb429950c0d.png','sb-photo','Confident student smiling in a classroom'),
      image_w('073b38d978bc5477128564cce9e98f91c2b98a52.png','sb-dots sb-dots--mtop'),
      image_w('073b38d978bc5477128564cce9e98f91c2b98a52.png','sb-dots sb-dots--mleft'),
    ]),
  ]),
], ['background_background'=>'classic','background_color'=>'#ffffff'], false);

/* ===== 8. TESTIMONIALS ===== */
$qmark = '1c6707dcb2cb2f9f6d7c10095273b752506df6ec.svg';
$qmarkA= 'acca82a8b5b2fb5199ba3f90087b474297a1e25f.svg';
$quote = function($markName,$body,$name,$active) use ($stars5){
  return con('sb-quote'.($active?' sb-quote--active':''), [
    image_w($markName,'sb-quote__mark'),
    text('<p>'.$body.'</p>'),
    con('sb-quote__foot', [
      heading($name,'sb-quote__name','div'),
      html_w($stars5),
    ]),
  ]);
};
$navPrev = '<button class="sb-cnav sb-cnav--prev" type="button" aria-label="Previous reviews"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>';
$navNext = '<button class="sb-cnav sb-cnav--next" type="button" aria-label="Next reviews"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>';
$sections[] = con('sb-testimonials', [
  con('sb-inner', [
    image_w('book-icon.png','sb-book sb-book--left'),
    heading('What Greenville Parents Are Saying','sb-section-title','h2'),
    image_w('book-icon.png','sb-book sb-book--right'),
  ]),
  con('sb-carousel', [
    html_w($navPrev,'sb-cnav-w'),
    con('sb-track', [
      $quote($qmark,'The caring approach and effective teaching strategies truly set this program apart from others we&rsquo;ve tried.','Luis M.',false),
      $quote($qmark,'Not only did my child improve academically, but their confidence in the classroom has grown tremendously.','Rebecca T.',false),
      $quote($qmarkA,'Within just a few weeks, my child&rsquo;s reading skills and confidence improved in ways we hadn&rsquo;t seen before.','Maria G.',false),
      $quote($qmark,'The personalized attention made all the difference—my child is now more focused, motivated, and excited to learn.','James L.',false),
      $quote($qmark,'We&rsquo;ve seen consistent academic growth, especially in reading and math, since starting tutoring here.','Sofia &amp; Daniel R.',false),
      $quote($qmark,'The sessions are engaging and structured, and my child now looks forward to learning every week.','Anthony P.',false),
    ]),
    html_w($navNext,'sb-cnav-w'),
  ]),
  html_w('<div class="sb-cdots" role="tablist" aria-label="Choose a review"></div>'),
], ['background_background'=>'classic','background_color'=>'#ffffff'], false);

/* ===== 9. VIDEO ===== */
$sections[] = con('sb-video', [
  image_w('hero-blob.png','sb-vblob sb-vblob--left'),
  image_w('hero-blob.png','sb-vblob sb-vblob--right'),
  con('sb-inner', [ heading('See Soulful Beginnings in Action','sb-section-title','h2') ]),
  con('sb-video__frame', [
    video_w('https://www.youtube.com/watch?v=aqz-KE-bpKQ',
      'http://localhost/Wordpress-elementor/wp-content/uploads/2026/07/maxresdefault.jpg', 68),
  ]),
], [], false);   // transparent: lets the red serving-section scallop show through in the gutters

/* ===== 10. SERVING ===== */
$areas = '<ul class="sb-areas">';
foreach(['Simpsonville','Mauldin','Greer','Taylors','Greenville area services'] as $a){
  $areas .= '<li><img src="'.$pin.'" alt="">'.$a.'</li>';
}
$areas .= '</ul>';
$sections[] = con('sb-serving', [
  con('sb-inner', [
    heading('Serving Families in Greenville, South Carolina','sb-section-title sb-section-title--light','h2'),
    text('<p>We proudly serve students and families in Greenville, SC and surrounding areas, offering both in-home and virtual tutoring services.</p>','sb-serving__lead'),
    con('sb-serving__grid', [
      html_w($areas),
      con('sb-serving__map', [ image_w('9b8da765bc1370e8a5ba184f964ff88d855ec1ad.png','','Map of Greenville service area') ]),
    ]),
  ]),
], [
  // Solid red only. No background image / overlay: the overlay rendered as an
  // Elementor ::before at opacity 0.5, colliding with our scallop ::before and
  // washing it out. The section red is painted via sba-design.css.
  'background_background'=>'classic','background_color'=>'#ff0214',
], false);

/* ===== 11. CTA ===== */
$gallery = [];
foreach(['253b2b6d0fa33dc9c7d138497ebb235b8773fb41.png','0931f2f7219ae0de929781d212d8f747b196c4aa.png','4ca8c913ecb48af5356c910ab6d452fadcb2b796.png','be8849c3bc37aa65934839434d52f9c8b4c31391.png','42c9e6a8fd6075e2ad677d47004a36102b86427a.png'] as $g){
  $gallery[] = image_w($g,'');
}
$sections[] = con('sb-cta', [
  con('sb-inner', [
    heading('Give Your Child the Confidence to Succeed','sb-section-title','h2'),
    text('<p>Partner with Soulful Beginnings Academy to unlock your child&rsquo;s full potential through personalized education.</p>','sb-cta__lead'),
    button('Book a Free Consultation','#','sb-btn sb-btn--lg'),
  ]),
  con('sb-gallery', $gallery),
], ['background_background'=>'classic','background_color'=>'#ffffff'], false);

/* ===== 12. FOOTER ===== */
$footSocial = '<div class="sb-footer__social"><a href="#" aria-label="Facebook">'.$icoFb.'</a><a href="#" aria-label="LinkedIn">'.$icoLi.'</a></div>';
$sections[] = con('sb-footer', [
  con('sb-inner', [
    image_w('8def6d18e0d025eb7dd81183c2d6f0f677e1fc76.png','sb-logo','Soulful Beginnings Academy'),
    navmenu_w('sb-nav sb-nav--footer','none'),
    con('sb-contact-grid', [
      con('sb-contact-col', [ heading('Phone','','h3'), text('<a href="tel:8642385679">864-238-5679</a>') ]),
      con('sb-contact-col', [ heading('Address','','h3'), text('<p>1234 Greenville, South Carolina,<br>OK 73545 United States</p>') ]),
      con('sb-contact-col', [ heading('Email','','h3'), text('<a href="mailto:soulfulbeginningsllc@gmail.com">soulfulbeginningsllc@gmail.com</a>') ]),
    ]),
    html_w($footSocial,'sb-footer__social-wrap'),
  ]),
  html_w('<div class="sb-copyright"><span>&copy; Copyright Soulful Beginnings Academy 2026. All Rights Reserved.</span><span>Privacy Policy | Sitemap</span></div>'),
], ['background_background'=>'classic','background_color'=>'#ffffff'], false);

/* Give the anchor-target sections real DOM ids so the nav menu (#about, #mission,
   #services) and the "#contact" buttons scroll to them. */
$idmap = ['sb-about'=>'about','sb-services'=>'services','sb-mission'=>'mission','sb-cta'=>'contact'];
foreach ($sections as &$sec) {
  $cls = $sec['settings']['_css_classes'] ?? '';
  foreach ($idmap as $c=>$id) { if (strpos($cls, $c) !== false) $sec['settings']['_element_id'] = $id; }
}
unset($sec);

/* ---------- create / update the page ---------- */
$title = 'Soulful Beginnings Academy';
$existing = get_page_by_title($title, OBJECT, 'page');
$postarr = ['post_title'=>$title,'post_status'=>'publish','post_type'=>'page','post_content'=>''];
if ($existing) { $postarr['ID']=$existing->ID; $pid = wp_update_post($postarr); }
else { $pid = wp_insert_post($postarr); }
if (is_wp_error($pid) || !$pid){ echo "Failed to create page\n"; exit(1); }

update_post_meta($pid, '_elementor_edit_mode', 'builder');
update_post_meta($pid, '_elementor_template_type', 'wp-page');
update_post_meta($pid, '_elementor_version', defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '3.20.0');
update_post_meta($pid, '_wp_page_template', 'elementor_canvas');
update_post_meta($pid, '_elementor_data', wp_slash(wp_json_encode($sections)));

/* set as front page */
update_option('show_on_front', 'page');
update_option('page_on_front', $pid);

/* clear elementor css cache so it regenerates */
if (class_exists('\Elementor\Plugin')) {
  try { \Elementor\Plugin::$instance->files_manager->clear_cache(); } catch (\Throwable $e) {}
}

echo "OK page_id={$pid} sections=".count($sections)." elements_total={$GID}\n";
echo "URL: ".get_permalink($pid)."\n";
