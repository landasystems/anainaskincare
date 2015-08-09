<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html>
<html b:version='2' class='v2' expr:dir='data:blog.languageDirection' xmlns='http://www.w3.org/1999/xhtml' xmlns:b='http://www.google.com/2005/gml/b' xmlns:data='http://www.google.com/2005/gml/data' xmlns:expr='http://www.google.com/2005/gml/expr'>
  <head>
    <b:include data='blog' name='all-head-content'/>

    <title>
      <b:if cond='data:blog.pageType == &quot;index&quot;'>
        <data:blog.pageTitle/>
        <b:else/>
        <b:if cond='data:blog.pageType != &quot;error_page&quot;'>
          <data:blog.pageName/> | <data:blog.title/>
          <b:else/>
          Page Not Found | <data:blog.title/> 
        </b:if>
      </b:if>
    </title>
    

    <b:if cond='data:blog.pageType == &quot;archive&quot;'>
      <meta content='noindex,noarchive' name='robots'/>
    </b:if>
    <meta charset='UTF-8'/>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>

<link href='http://fonts.googleapis.com/css?family=Playfair+Display:300,400,700|Raleway:300,400,700|Pacifico|Montserrat:400,700&amp;subset=cyrillic' rel='stylesheet' type='text/css'/>
<link href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css' rel='stylesheet' type='text/css'/>
<link href='//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' id='fontawesome' rel='stylesheet' type='text/css'/>

<b:skin><![CDATA[

*------------------------------------

Blogger Template Style 
Template name :Square Magazine
Designer : VeeThemes.com
Site : http://www.veethemes.com
Verion : Full Version 

--------------------------------------*/
*,*:before,*:after{box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;}
/*------------------------------------------------------
Variable @ Template Designer
--------------------------------------------------------
<Group description="Header Text" selector="h1,h2,h3,h4,h5,h6">
<Variable name="header.font" description="Font" type="font"
default="normal normal 12px Arial, Tahoma, Helvetica, FreeSans, sans-serif" value="Raleway, sans-serif"/>
<Variable name="header.text.color" description="Text Color" type="color" default="#222222" value="#222222"/>
</Group>

<Variable name="keycolor" description="Main Color" type="color" default="#757575" value="#757575"/>
<Group description="Body-background" selector="body">
<Variable name="body.background.color" description="Body Color" type="color" default="#f3f3f3" value="#ffffff"/>
<Variable name="primary.background.color" description="Main background Color" type="color" default="#28c9bc" value="#222222"/>
<Variable name="secondary.background.color" description="Secondary background Color" type="color" default="#28c9bc" value="#F0BF3E"/>
<Variable name="tertiary.background.color" description="Tertiary background Color" type="color" default="#28c9bc" value="#43B0FF"/>
</Group>

------------------------------------------------------*/

/*****************************************
reset.css
******************************************/
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td, figure {    margin: 0;    padding: 0;}
article,aside,details,figcaption,figure,
footer,header,hgroup,menu,nav,section {     display:block;}
table {    border-collapse: separate;    border-spacing: 0;}
caption, th, td {    text-align: left;    font-weight: normal;}
sup{    vertical-align: super;    font-size:smaller;}
code{    font-family: 'Courier New', Courier, monospace;    font-size:12px;    color:#272727;}
::selection {  background: #333;  color: #fff;  }
::-moz-selection {  background: #333;  color: #fff;  }
a img{	border: none;}
ol, ul { padding: 10px 0 20px;  margin: 0 0 0 35px;  text-align: left;  }
ol li { list-style-type: decimal;  padding:0 0 5px;  }
ul li { list-style-type: square;  padding: 0 0 5px;  }
ul ul, ol ol { padding: 0; }
h1, h2, h3, h4, h5, h6 { font-family: $(header.font); font-weight: normal;}
.post-body h1 { line-height: 48px; font-size: 42px; margin: 10px 0; }
.post-body h2 { font-size: 36px; line-height: 44px; padding-bottom: 5px; margin: 10px 0; }
.post-body h3 { font-size: 32px; line-height: 40px; padding-bottom: 5px; margin: 10px 0; }
.post-body h4 { font-size: 28px; line-height: 36px; margin: 10px 0;  }
.post-body h5 { font-size: 24px; line-height: 30px; margin: 10px 0;  }
.post-body h6 { font-size: 18px; line-height: 24px; margin: 10px 0;  }

/*****************************************
Global Links CSS
******************************************/
a{ color: #555; outline:none; text-decoration: none; }
a:hover, a:focus { color: #222; text-decoration:none; }
body{ background: $(body.background.color); color: #888; padding: 0; font-family: 'raleway', sans-serif; font-size: 15px; line-height: 25px; }
.clr { clear:both; float:none; }

/*****************************************
Wrappers
******************************************/
.ct-wrapper { padding: 0px 20px; position: relative; max-width: 1080px; margin: 0 auto; }
.outer-wrapper { margin: 50px 0 25px;  margin-top: 50px; position: relative; }
.header-wrapper {
  display: inline-block;
  float: left;
  width: 100%;
  z-index: 100;
  position: relative;
  background-color: #F9F9F9;
-webkit-box-shadow: 0px -19px 20px 10px #000;
  box-shadow: 0px -19px 20px 10px #000;
-moz-box-shadow: 0px -19px 20px 10px #000;
}
.main-wrapper {   width: 70%;float: left;}
div#content {
	margin:0;
  margin-right: 20px;
}
.sidebar-wrapper { width: 30%;float: right;}

/**** Layout Styling CSS *****/
body#layout .header-wrapper { margin-top: 40px; }
body#layout .outer-wrapper, body#layout .sidebar-wrapper, body#layout .ct-wrapper { margin: 0; padding: 0; }
body#layout #About { width: 100%; }
.section, .widget{margin:0;}
#layout div#navigation {
  display: block;
  width: auto;
}

/*****************************************
Top Header CSS
******************************************/

.hdr-top {
  background-color: #333;
  position: relative;
  z-index: 1000000;
}

.top_menu li a {
  padding:7px 5px;color:#999;display:block;
}

.top_menu ul li {
  padding: 0;
  margin: 0;
  margin-right: 25px;
  list-style: none;
  display: inline-block;
  font-size: 11px;
  font-family: montserrat;
  text-transform: uppercase;
}
.top_menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}


.one-half {
width: 45%;
float:left;
}




.header-social {
margin:0;
margin-top:-70px;
margin-right: 50px;
padding:0;
position: absolute;
right:0;
display:inline-block;
visibility: visible;
  transition: all .2s ease-in-out;
  -webkit-transition: all .2s ease-in-out;
  -moz-transition: all .2s ease-in-out;
  opacity: 0;
}


.visible {
  visibility: visible;
  opacity: 1;
margin-top:0px;
}


.one-half.last {
float: right;
}

.social-media-container .notice {
  display: inline-block;
  float: left;
  margin-right: 2px;
  font-size: 12px;
}
.social-media-container .notice i {
  margin-right: 5px;
}
.header-social li {
  margin-right: 1px;
  list-style: none;
  float: left;
  margin: 0;
  font-size: 12px;
}

.header-social li a {
  text-decoration: none;
  color: #999;
  text-align: center;
  display: block;
  padding: 7px;

}

.header-social li a:hover,.top_menu li a:hover {
  color: #eee;
  border-color: #eee;
  transition: all .2s ease-in-out;
-webkit-transition: all .2s ease-in-out;
-moz-transition: all .2s ease-in-out;
-o-transition: all .2s ease-in-out;
}


.header-social li a .fa-facebook {
padding-left: 3px;
padding-right: 3px;
}
.header-social li a .fa-tumblr {
padding-left: 3px;
padding-right: 3px;
}

input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
border-radius: 0;
font-size: 13px;
-moz-box-shadow: 0px 0px 0px rgba(0,0,0,0), 0 0 10px rgba(0, 0, 0, 0) inset;
-webkit-box-shadow: 0px 0px 0px rgba(0,0,0,0), 0 0 10px rgba(0, 0, 0, 0) inset;
box-shadow: 0px 0px 0px rgba(0,0,0,0), 0 0 10px rgba(0, 0, 0, 0) inset;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
-ms-box-sizing: border-box;
box-sizing: border-box;
line-height: 1em;
padding: 15px 10px!important;
height: 50px;
vertical-align: middle;
background: #fff;
border: none;
-webkit-transition: all 0.2s linear 0s;
-moz-transition: all 0.2s linear 0s;
-ms-transition: all 0.2s linear 0s;
-o-transition: all 0.2s linear 0s;
transition: all 0.2s linear 0s;
}
.search-include {
  float: right;
  height: auto;
  position: relative;
  margin-top: 5px;
  margin-left: 25px;
}
.search-button a {
  display: block;
  text-decoration: none;
  color: #555;
  border-top: none;
  height: 30px;
  width: 30px;
  padding-top: 4.5px;
  font-size: 13px;
  text-align: center;
  position: relative;
}

#search-popup {
padding: 30px 30px;
text-align: left;
max-width: 1000px;
margin: 40px auto;
position: relative;
}
.mfp-hide {
display: none !important;
}

#search-popup {
padding: 30px 30px;
text-align: left;
max-width: 1000px;
margin: 40px auto;
position: relative;
margin-top: 12%;
}
.popup-search form, .popup-search form input {
margin: 0;
position: relative;
}
#search-popup .ft {
text-align: left;
border-bottom: 1px solid #ebebeb;
}

.popup-search input.ft {
width: 100%;
background: none;
height: 85px;
font-size: 40px;
border: none;
}
.mfp-wrap {
top: 0;
left: 0;
width: 100%;
height: 100%;
z-index: 99999;
position: fixed;
outline: none !important;
-webkit-backface-visibility: hidden;
}
.mfp-bg {
top: 0;
left: 0;
width: 100%;
height: 100%;
z-index: 99999;
overflow: hidden;
position: fixed;
background: #0b0b0b;
opacity: 0.8;
filter: alpha(opacity=80);
}
.my-mfp-zoom-in.mfp-ready.mfp-bg {
opacity: 1;
}

.my-mfp-zoom-in.mfp-bg {
opacity: 0;
-webkit-transition: opacity 0.3s ease-out;
-moz-transition: opacity 0.3s ease-out;
-o-transition: opacity 0.3s ease-out;
transition: opacity 0.3s ease-out;
background: #ffffff;
}
.mfp-container {
/* text-align: center; */
/* position: absolute; */
width: 100%;
height: 100%;
left: 0;
top: 0;
padding: 0 8px;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
}
.mfp-auto-cursor .mfp-content {
cursor: auto;
}
.mfp-content {
position: relative;
display: inline-block;
vertical-align: middle;
margin: 0 auto;
text-align: left;
z-index: 1045;
width: 100%;
}
.my-mfp-zoom-in.mfp-ready .zoom-anim-dialog {
opacity: 1;
-webkit-transform: scale(1);
-moz-transform: scale(1);
-ms-transform: scale(1);
-o-transform: scale(1);
transform: scale(1);
}

.my-mfp-zoom-in .zoom-anim-dialog {
opacity: 0;
-webkit-transition: all 0.2s ease-in-out;
-moz-transition: all 0.2s ease-in-out;
-o-transition: all 0.2s ease-in-out;
transition: all 0.2s ease-in-out;
-webkit-transform: scale(0.8);
-moz-transform: scale(0.8);
-ms-transform: scale(0.8);
-o-transform: scale(0.8);
transform: scale(0.8);
}
button.mfp-close, button.mfp-arrow {
overflow: visible;
cursor: pointer;
background: transparent;
border: 0;
-webkit-appearance: none;
display: block;
outline: none;
padding: 0;
z-index: 1046;
-webkit-box-shadow: none;
box-shadow: none;
}

.mfp-close {
width: 44px;
height: 44px;
line-height: 44px;
position: absolute;
right: 0;
top: 0;
text-decoration: none;
text-align: center;
opacity: 0.65;
filter: alpha(opacity=65);
padding: 0 0 18px 10px;
color: white;
font-style: normal;
font-size: 28px;
font-family: Arial, Baskerville, monospace;
color: #333;
}



.blog-author {
float: right;
color: #bbb;
font-size: 11px;
line-height:1;
margin: 10px 0px 0px 0px;
}


/*****************************************
Header CSS
******************************************/
.navigation {
  border-top: 1px solid #ddd;
  padding: 2px 0 3px;
  display: TABLE;
  width: 100%;
}

#header {
  display: block;
text-align:center;
}
#header-inner{ margin:20px 0; padding: 0; }

#header h1 {
  font-family: "raleway",cursive;
  font-size: 48px;
  font-weight: 300;
  line-height: 1.6;
display:inline-block;
}

#header h1 a, #header h1 a:hover {  color: #333;  display:block; padding:15px 10px;}

#header p.description{ color: #333; font-size: 12px; font-style: italic; text-shadow: 1px 1px #FFFFFF; margin: 0; padding: 0; text-transform:capitalize; }

#header img{   border:0 none; background:none; width:auto; height:auto; margin:0 auto;  }

a.logo-img {
  display: block;
  padding: 25px 0;
}

/*****************************************
Blog Post CSS
******************************************/
.article_container {
  display: inline-block;
  height: 100%;
  width: 100%;
}

.article_image{position:relative;}

.article_image img{width:100%;}

.article_inner {
width: 93%;
  display: inline-block;
  margin: 0 auto;
  margin-top: -60px;
  left: -1px;
  position: relative;
  background-color: #fff;
  padding: 35px 25px;
  z-index: 1000;
}

.article_header {
margin-bottom: 20px;
}
.article_header a {
  color: #444;
}

.article_header h2,h1.article_post_title{
  font-size: 32px;
  line-height: 1.4;
  margin: 0;
  padding: 0;
margin-bottom:10px;
  font-weight: bold;
}
h1.article_post_title {
  font-size: 38px;color:#777;
}

.meta{font-family:montserrat,sans-serif;font-size:10px;  text-transform: uppercase;}
.meta,.meta a{  color: #999;}


.meta.post_meta span {
  margin-right: 5px;
}

.meta.meta_tag {
  font-size: 11px;
}
.meta.meta_tag a:nth-child(n+3) {
display:none;
}
.meta.meta_tag .fa {
  margin-right: 5px;
  font-size: 9px;
color: #F0BF3E;
position:relative;
top:0
}

.meta-item.rdmre {
}

.article_excerpt p {
  margin-bottom: 40px;
}

.meta-item.rdmre a {
  display: inline-block;
  line-height: 1;
  color: #444;
font-size:11px;
  background-color: transparent;
  border: 1px solid #CCC;
  padding: 10px 20px;
  position: relative;
  text-align: center;
  border-radius: 4px;
  font-family: montserrat,sans-serif;
  text-transform: uppercase;
}

.article_container:hover .meta-item.rdmre a {
  background-color: #F0BF3E;
  border-color: #F0BF3E;
  color: #fff;
}

.article_container a {
  transition: all .3s ease-in-out;
  -webkit-transition: all .3s ease-in-out;
  -moz-transition: all .3s ease-in-out;
  -o-transition: all .3s ease-in-out;
  -ms-transition: all .3s ease-in-out;
}
.article_container:hover .article_header h2 a {
  color: #888;
}


.meta-item.share {
  display: none;
}



.post-outer {
  margin-bottom: 40px;
}
.post{ padding:0; }
.post-title { font-size: 28px; line-height: normal; margin: 0; padding: 0 0 10px; text-decoration: none; text-transform: capitalize; }
.post-title a{ color:#333; }
.post-title a:hover{ color: #2980B9; }

.post-body { word-wrap:break-word;  }
.post-header { border-bottom: 1px solid #F5F5F5; font-size: 12px; line-height: normal; margin: 0 0 10px; padding: 0 0 10px; text-transform: capitalize; }
.post-header a { color: #999; }
.post-header a:hover{ color: #2980B9; }

.post-footer {
  margin-top: 30px;
}
.post-body img{ height: auto; max-width: 100%; }


h3.content-header {
  text-align: center;
  margin: 0 auto 50px;
font-weight:bold;
  text-transform: uppercase;
  letter-spacing: 5px;
}

.margin-30 {
  margin-bottom: 30px;
}

/***** Page Nav CSS *****/
#blog-pager { display: inline-block; margin: 20px 0; overflow: visible; padding: 0; width: 100%; }

.showpageOf, .home-link {  display:none;  }
.showpagePoint {  background: #2980B9;  color: #FFFFFF;  margin: 0 10px 0 0;  padding: 5px 10px;  text-decoration: none;  border-radius: 3px; -moz-border-radius: 3px; -o-border-radius: 3px; -webkit-border-radius: 3px;  }
.showpage a, .showpageNum a { background: #333; color: #FFFFFF; margin: 0 10px 0 0; padding: 5px 10px; text-decoration: none; border-radius: 3px; -moz-border-radius: 3px; -o-border-radius: 3px; -webkit-border-radius: 3px; }
.showpage a:hover, .showpageNum a:hover {  background: #2980B9;  color: #fff;  border-radius: 3px;  -moz-border-radius: 3px;  -o-border-radius: 3px;  -webkit-border-radius: 3px;  text-decoration: none;  }
#blog-pager-newer-link { padding: 20px 5%; position: relative; text-align: left; width: 40%; }
#blog-pager-newer-link:before { content: "\00AB"; font-size: 30px; left: 0; position: absolute; top: 13%; }
#blog-pager-older-link { padding: 20px 5%; position: relative; text-align: right; width: 40%; }
#blog-pager-older-link:before { content: "\00BB"; font-size: 30px; position: absolute; right: 0; top: 13%; }
#blog-pager-newer-link .newer-text, #blog-pager-older-link .older-text { display: block; color: #999; }

/*****************************************
Post Highlighter CSS
******************************************/
blockquote {
  border-left: 5px solid #333;
  font-size: 14px;
  padding: 15px 25px;
  background-color: #F5F5F5;
}
/*****************************************
Sidebar CSS
******************************************/
.sidebar { margin: 0; padding: 0; display: block; }

.sidebar h2 {
font-size: 14px;
  text-transform: uppercase;
  font-weight: 400;
  display: table;
  color: #333;
  overflow: hidden;
}

.sidebar h2, .footer h2 {
font-family: montserrat, sans-serif;
  letter-spacing: 0px;
  line-height: 1;
  padding: 0 10px;
  margin-bottom: 45px;
  background-color: #fff;
}


.sidebar .widget {
  padding: 0 20px;
  clear: both;
  font-size: 13px;
  line-height: 23px;
  margin-bottom: 40px;
overflow:hidden;
position:relative;
}

.sidebar .widget:before {
  content: "";
  position: absolute;
  background-color: #333;
  height: 14px;
  width: 90%;
  left: 10px;
}


.sidebar ul { margin: 0; padding: 0; list-style: none; }
.sidebar li {line-height: normal;
  list-style: none !important;
  margin: 3px 0;}

/*****************************************
Footer Credits CSS
******************************************/
.footer_bottom {
position: relative;
margin-top: 40px;
}

.footer-attribution{
padding:30px 0;
font-size:13px;
}

.scroll_header {
  float: right;
  position: absolute;
  bottom:25px;
  right: 10px;
  width: 30px;
  height: 30px;
  background-color: #222;
  text-align: center;
  font-size: 10px;
}

.scroll_header a {
  color: #fff;
  display: block;
  padding: 2px 0;
}

.footer-menu {
  float: left;
  margin-top: 40px;
  margin-left: 30px;
}
.footer-menu ul {
  margin: 0;
  padding: 0;
}
.footer-menu ul li {
  list-style: none;
  display: inline-block;
  margin-right: 15px;
padding:0;
  font-size: 14px;
}


/*------- -------- -----------
		featured Posts 
-------- -  ------------ -------*/
.blog_featured_posts {
  counter-reset: trackit;
}
.blog_featured_post {
  margin-bottom: 35px;
  counter-increment: trackit;
  position: relative;
}

.blog_featured_post::before {
  content: counters(trackit,"");
  float: left;
  width: 30px;
  height: 40px;
  text-align: center;
  line-height: 1.7;
  font-size: 20px;
  font-weight: 500;
  font-family: montserrat,sans-serif;
  background-color:$(secondary.background.color);
  color: #fff;
  position: absolute;
  top: -10px;
  left: -10px;
  z-index: 1;
}

img.img_fade {
  max-width: 100%;
  opacity: 0;
  display: none;
}

.feat_link {
  display: block;
  position: relative;
  width: 100%;
  height: 145px;
  overflow: hidden;
}

.feat-img {
  width: 100%;
  height: 100%;
  top: 0;
  position: absolute;
  background-position: 50%;
  background-repeat: no-repeat;
  background-size: cover;
overflow: hidden;
}

.feat-info-wrapper span {
  font-size: 12px;
  font-family: montserrat,sans-serif;
  text-transform: uppercase;
  font-weight: bold;
  line-height: 1.5;
  letter-spacing: 0px;
  margin: 7px 0 7px;
  border: 0;
  display: block;
}

.feat-info-wrapper span a{color:#333}

a.featured_rd_mre {
  font-size: 13px;
  color: #3AC3FF;
  font-family: playfair display,sans-serif;
}


/*--------- [slider] --------*/
figure.slider__item {position: relative;}
figcaption.slide__caption {position: absolute;top: 0;left: 0px;padding: 40px;width:100%;height:100%;}
figcaption.slide__caption:before {
content: "";
position: absolute;
top: 0;
left: 0;
bottom: 0;
width: 100%;
height: 100%;
background: rgba(0, 0, 0, 0.15);
-webkit-box-shadow: inset 0px 0px 150px 0px #000;
-moz-box-shadow: inset 0px 0px 150px 0px #000;
-ms-box-shadow: inset 0px 0px 150px 0px #000;
box-shadow: inset 0px 0px 150px 0px #000;
transition:all .2s ease-in;
-o-transition:all .2s ease-in;
-ms-transition:all .2s ease-in;
-moz-transition:all .2s ease-in;
-webkit-transition:all .2s ease-in;
}
figure.slider__item:hover .slide__caption:before {
background: transparent;
transition:all .2s ease-in;
-o-transition:all .2s ease-in;
-ms-transition:all .2s ease-in;
-moz-transition:all .2s ease-in;
-webkit-transition:all .2s ease-in;
}
figure.slider__item a.post__link {
position: relative;
display: block;
}
.post__description {
position: relative;
z-index:100;
height: 100%;
width: 100%;
}
.post__description p a {
display: inline-block;
background: $(primary.background.color);
  color: #fff;
  line-height: 1;
  letter-spacing: 1px;
  font-size: 11px;
  text-transform: uppercase;
  padding: 4px 6px;
  font-family: montserrat,sans-serif;
  border-radius: 1px;
  top: 45%;
  position: absolute;
}
.post__description h2 {
top: 60%;
  position: absolute;
  font-weight: 700;
}
.post__description h2 a{color:#fff;}
.img__container {
display: block;
height: 320px;
width: 100%;
float:left;
background-size: cover;
background-position: 50%;
}


/*****************************************
Custom Widget CSS
******************************************/
/***** Search Form *****/
#searchform fieldset { background: #F1F4F9; border: 1px solid #F1F4F9; color: #888888; width: 98%; }
#searchform fieldset:hover { background: #fff; }
#s { background: url("http://3.bp.blogspot.com/-Mu6D1ld_3TE/U35bF1XXIVI/AAAAAAAADBM/VaHEtkyX3MA/s1600/sprites.png") no-repeat scroll right -60px rgba(0, 0, 0, 0); border: 0 none; color: #888888; float: left; margin: 8px 5%; padding: 0 10% 0 0; width: 80%; }

/***** Custom Labels *****/
.cloud-label-widget-content { display: inline-block; text-align: left; }
.cloud-label-widget-content .label-size { display: inline-block; float: left; font-size: 10px; font-family: Verdana,Arial,Tahoma,sans-serif; font-weight: bold; line-height: normal; margin: 5px 5px 0 0; opacity: 1; text-transform: uppercase; }
.cloud-label-widget-content .label-size a { color: #000 !important; float: left; padding: 5px; }
.cloud-label-widget-content .label-size:hover a { color: #555 !important; }
.cloud-label-widget-content .label-size .label-count { color: #555; padding: 5px 0; float: left; }

.Label li{
transition: all .2s ease-in-out;
-webkit-transition: all .2s ease-in-out;
-moz-transition: all .2s ease-in-out;
-o-transition: all .2s ease-in-out;
}
.Label li:hover {
  padding-left: 10px;
}
#Label1 li a{position:relative;}
#Label1 li a:before {
  content: "\f016";
  font-family: Fontawesome;
  margin-right: 10px;
  color: #797979;
}


/***** Popular Post *****/
.PopularPosts .item-thumbnail img {
  display: block;
  float: left;
  height: 95px;
  width: 95px;
}
.PopularPosts .item-title {
  padding-bottom: .2em;
  font-size: 14px;
  text-transform: capitalize;
}
.item-snippet {
  display: none;
}

/*--------- [sidebar newsletter ]----------*/
div#blog_newsletter h5{font-size: 14px;margin-bottom: 10px;}
div#blog_newsletter p{font-size:12px; line-height:1.7;margin-bottom: 20px;}

div#blog_newsletter input#subbox {
  line-height: 1;
  background: #eee;
  border: none;
  border-radius: 2px;
  font-size: 13px;
  letter-spacing: 1px;
  min-height: 30px;
  height: 38px;
  margin: 0 0 20px;
  padding: 10px 10px!important;
  width: 100%;
  box-shadow: none;
  -webkit-box-shadow: none;
  -moz-box-shadow: none;
  -ms-box-shadow: none;
  outline: 0;

}
div#blog_newsletter input#subbutton {padding: 10px;line-height:1;width: 100%;text-transform: uppercase;margin-bottom: 5px;box-shadow: none;outline: 0;color: #fff;display: inline-block;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;background-image: none;border: 1px solid transparent;border-radius: 4px;font-size: 13px;letter-spacing: 2px;font-weight: 400;background: $(primary.background.color);}
div#logo img {max-height:65px;}

.subscribe-footer {
  background: rgb(243, 243, 243);
  padding: 70px 20px 65px;
  text-align: center;
  border-top: 1px solid rgb(238, 238, 238);
  position: relative;
  z-index: 1;
  font-family: 'montserrat',sans-serif;
}
.subscribe-footer h1 {
  margin-bottom: 15px;
  font-family: 'raleway',sans-serif;
}

.subscribe-footer p {
  margin-bottom: 10px;
  color: rgb(153, 153, 153);
}

input#sub_box,input#sub_button{
outline:none;
  border-image-source: initial;
  border-image-slice: initial;
  border-image-width: initial;
  border-image-outset: initial;
  border-image-repeat: initial;
}

input#sub_box {
  background: rgb(255, 255, 255);
  border: 1px solid rgb(221, 221, 221);
  color: rgb(119, 119, 119);
  display: inline-block;
  max-width: 100%;
  outline: none;
  padding: 10px 8px;
  width: 360px;
  border-radius: 4px;
  height: 37px;
}

input#sub_button {
background-color: #616161;
  border: 1px solid #6F6F6F;

  margin-left: 3px;
  color: rgb(255, 255, 255);
  display: inline-block;
  cursor: pointer;
  border-radius: 2px;
  height: 35px;
  width: 92px;
  font-size: 14px;
}

.post_tags {
  margin-bottom: 20px;  font-size: 12px;
}
.post_tags .fa {
color: #F0BF3E;
  margin-right: 5px;
}
.meta.post_tags a {
  text-transform: none;
  text-decoration: underline;
  margin-right: 1px;
}

/*-----------[ share-wrapper ]-----------*/
.share-wrapper, .authorboxwrap {margin-bottom: 50px;}
.share-wrapper ul {padding: 0;margin: 0 auto;display:table;text-align: center;}
.share-wrapper li {list-style: none;display: inline-block;float:left;margin-right:0;padding: 0;margin-bottom: 30px;}
.share-wrapper li:first-child {display: block;margin-bottom: 20px;font-size: 16px;}
.share-wrapper li a{display:block;text-align: center;}
.share-wrapper li a i {
display: none;
color: #555;
width: 35px;
height: 35px;
padding: 9px;
font-size: 16px;
background: #F0F0F0!important;
border: 1px solid #DEDEDE;
}
.share-wrapper{margin-bottom:30px;}

.share-wrapper span {
  display: block;
  font-size: 12px;
  font-family: montserrat;
  background-color: #eee;
  line-height: 1;
  padding: 8px 25px;
  color: #fff;
}

li.facebook_share span {background-color: #3b5998;}
li.twitter_share span {background-color: #00aced;}
li.pinterest_share span{background-color: #cb2027;}
li.google_share span {background-color: #dd4b39;}
li.linkedin_share span {background-color: #007bb6;}

.share-wrapper > .title {
  text-align: center;
  margin-bottom: 30px;
}



/*------[author-box ]-------*/
.avatar-container {width: 170px;float: left;}
.avatar-container img {width: 125px;height: auto;border: 5px solid transparent;box-shadow: 0px 0px 20px -5px #000;-moz-box-shadow: 0px 0px 20px -5px #000;-webkit-box-shadow: 0px 0px 20px -5px #000;-ms-box-shadow: 0px 0px 20px -5px #000;-o-box-shadow: 0px 0px 20px -5px #000;}
.author_description_container {margin-left: 170px;}
.author_description_container h4 {font-size: 16px;display: block;margin-bottom: 10px;}
.author_description_container h4 a{color: #333;}
.author_description_container p {font-size: 12px;line-height: 1.7;margin-bottom: 15px;}
.authorsocial a {display: inline-block;margin-right: 5px;text-align: center;float:left;margin-right:2px;}
.authorsocial a i {width: 30px;height: 30px;padding: 8px 9px;display: block;background: #E9E9E9!important;color: #555;}
/*------*|*|*| Related Posts *|*|*|----------*/
div#related-posts {font-size: 16px;display: inline-block;width: 100%;}
div#related-posts h5 {font-size: 16px;text-transform: uppercase;margin: 0 0 25px;padding-bottom:15px;font-weight: 900;letter-spacing: 1px;text-align:center;position:relative;}
div#related-posts h5:after {content: "";position: absolute;width: 4px;height: 4px;background: #222;border-radius: 50%;bottom: 0;left: 47%;box-shadow: 1em 0px 0px 0px #222,2em 0px 0px 0px #222;}
div#related-posts ul {padding: 0;margin: 0;}
div#related-posts ul li {
  list-style: none;
  display: block;
  float: left;
  width: 32.555%;
  padding: 0;
  margin-left: 2px;
  text-align: center;
  position: relative;
}
div#related-posts ul li:first-child {margin-left: 0;}
div#related-posts img {
  padding: 0;
  width: 100%;
}
a.related-thumbs {position: relative;display: block;}
a.related-thumbs:before{opacity:1;}
a.related-title {
font-size: 13px;
line-height: 1.7;
display: block;
padding-top: 0;
margin: 10px 6px 0;
color: #333;}




/***** Blogger Contact Form Widget *****/
.contact-form-email, .contact-form-name, .contact-form-email-message, .contact-form-email:hover, .contact-form-name:hover, .contact-form-email-message:hover, .contact-form-email:focus, .contact-form-name:focus, .contact-form-email-message:focus { background: #F8F8F8; border: 1px solid #D2DADD; box-shadow: 0 1px 1px #F3F4F6 inset; max-width: 300px; color: #999; }
.contact-form-button-submit { background: #000; border: medium none; float: right; height: auto; margin: 10px 0 0; max-width: 300px; padding: 5px 10px; width: 100%; cursor: pointer; }
.contact-form-button-submit:hover { background: #2980B9; border: none; }

/***** Profile Widget CSS *****/
.Profile img { border:1px solid #cecece; background:#fff; float:left; margin:5px 10px 5px 0; padding: 5px; -webkit-border-radius: 50%;	-moz-border-radius: 50%; border-radius: 50%; }
.profile-data { color:#999999; font:bold 20px/1.6em Arial,Helvetica,Tahoma,sans-serif; font-variant:small-caps; margin:0; text-transform:capitalize;}
.profile-datablock { margin:0.5em 0;}
.profile-textblock { line-height:1.6em; margin:0.5em 0;}
a.profile-link { clear:both; display:block; font:80% monospace; padding:10px 0; text-align:center; text-transform:capitalize;}

/***** Meet The Author *****/
#About { background: #FFFFFF; display: inline-block; padding: 25px 3%; width: 94%; }
#About .widget-content { position: relative; width: 100%; }
#About .widget-content .main-wrap { width: auto; margin-right: 370px; }
#About .widget-content .main-wrap .info { float: left; position: relative; width: 90%; padding: 10px 5%; }
#About .widget-content .side-wrap { width: 340px; float: right; text-align: center; }
#About .widget-content .main-wrap .info h5 { border-bottom: 1px solid #F1F4F9; color: #000000; font-size: 16px; font-weight: bold; margin: 0 0 10px; padding: 0 0 5px; text-transform: capitalize; } 
#About .widget-content .main-wrap .info p { color: #555; font-style: italic; } 
#About .widget-content .side-wrap .author-img { border: 1px solid #CECECE; height: 150px; vertical-align: bottom; width: 150px; -webkit-border-radius: 50%;	-moz-border-radius: 50%; border-radius: 50%; }
ul.author-social { display: inline-block; margin: 10px 0 0; padding: 0; }
ul.author-social li { background: url("http://3.bp.blogspot.com/-Mu6D1ld_3TE/U35bF1XXIVI/AAAAAAAADBM/VaHEtkyX3MA/s1600/sprites.png") no-repeat; display: inline-block; font-weight: bold; font-size: 12px; line-height: 16px; list-style: none; padding: 0 20px; }
ul.author-social li.facebook { background-position: 0 -80px; }
ul.author-social li.twitter { background-position: 0 -100px; }
ul.author-social li.googleplus { background-position: 0 -120px; }
ul.author-social li.rss { background-position: 0 -176px; }
ul.author-social li a { color: #000; }
ul.author-social li a:hover { color: #666; }

/*****************************************
Comments CSS
******************************************/
.comments { margin-top: 30px; }
.comments h4 { font-size: 20px; margin: 0 0 18px; text-transform: capitalize; }
.comments li {
  list-style: none;
}
.comments .comments-content .comment-thread ol { overflow: hidden; margin: 0; }
.comments .comments-content .comment:first-child { padding-top: 0; }
.comments .comments-content .comment { margin-bottom: 0; padding-bottom: 0; }
.comments .avatar-image-container { max-height: 60px; width: 60px; }
.comments .avatar-image-container img { max-width: 40px; width: 100%; }
.comments .comment-block { background: #fff; margin-left: 40px; padding: 1px 0 0 20px; border-radius: 2px; -moz-border-radius: 2px; -webkit-border-radius: 2px; }
.comments .comments-content .comment-header a { color: #333; text-transform: capitalize; }
.comments .comments-content .user {   display: inline-block;
  font-weight: bold;
  font-size: 12px;
  margin-bottom: 10px;
  margin-right: 10px;
}
.comments .comments-content .datetime { margin-left: 0; }
.comments .comments-content .datetime a { font-size: 12px; text-transform: uppercase; }
.comments .comments-content .comment-header, .comments .comments-content .comment-content { margin: 0 20px 0 0; }
.comments .comment-block .comment-actions { display: block; text-align: right; }
.comments .comment .comment-actions a { border-radius: 2px; -moz-border-radius: 2px; -webkit-border-radius:2px; background: $(secondary.background.color); color: #FFFFFF; display: inline-block; font-size: 12px; line-height: normal; margin-left: 1px; padding: 5px 8px; }
.comments .comment .comment-actions a:hover { text-decoration: none; }
.comments .thread-toggle { display: none; }
.comments .comments-content .inline-thread { border-left: 1px solid #F4F4F4; margin: 0 0 20px 35px !important; padding: 0 0 0 20px; }
.comments .continue { display: none; }
.comments .comments-content .icon.blog-author {
  display: none;
}
.comment-thread ol { counter-reset: countcomments; }
.comment-thread li:before { color: $(secondary.background.color); content: counter(countcomments, decimal); counter-increment: countcomments; float: right; font-size: 22px; padding: 15px 20px 10px; position: relative; z-index: 10; }
.comment-thread ol ol { counter-reset: contrebasse; }
.comment-thread li li:before { content: counter(countcomments,decimal) "." counter(contrebasse,lower-latin); counter-increment: contrebasse; float: right; font-size: 18px; }

/*****************************************
Responsive styles
******************************************/
@media screen and (max-width: 960px) {
.main-wrapper { margin-right:0; width:100%; }
.sidebar-wrapper{ float: left; width: auto; margin-top: 30px; }
}
@media screen and (max-width: 768px){
.article_header h2, h1.article_post_title{font-size:22px;}
.article_excerpt p{font-size:13px;}
.article_inner{margin-top:-25px;}
#comment-editor { margin:10px; }
input#sub_box {margin-bottom: 10px;  text-align: center;}
input#sub_button {width: 160px;}
.subscribe-footer h1 {font-size: 22px;}
}
@media screen and (max-width: 500px){
}
@media screen and (max-width: 420px){
.comments .comments-content .datetime{    display:block;    float:none;    }
.comments .comments-content .comment-header {    height:70px;    }
}
@media screen and (max-width: 320px){
.post-body img{  max-width: 230px; }
.comments .comments-content .comment-replies {    margin-left: 0;    }
}

/*****************************************
Hiding Header Date and Feed Links
******************************************/
h2.date-header, span.blog-admin{display:none;}

]]></b:skin>
<b:if cond='data:blog.pageType != &quot;index&quot;'>
<style type='text/css'>

</style>
</b:if>
<b:if cond='data:blog.pageType == &quot;static_page&quot;'>
<style type='text/css'>
#blog-pager, #attri_bution { display: none !important; }
</style>
</b:if>

<style id='responsive-menu' type='text/css'>



/*----- Menu -----*/
@media screen and (min-width: 860px) {

/*****************************************
Main Menu CSS
******************************************/
a.toggle-nav {
  display: none;
}
.nav-menu { margin: 0 auto; padding: 0; width: auto; z-index: 299;float:left }
.nav-menu ul{ list-style:none;  margin:0; padding:0; z-index: 999; }

.nav-menu ul li {
  display: inline-block;
  line-height: 1;
  list-style: none;
  padding: 0;
  margin-right: 15px;
}
.nav-menu li a {
  color: #333;
  display: block;
  padding: 15px 5px;
  position: relative;
  text-decoration: none;
}
}
  
  .nav-menu li a{
  text-transform: uppercase;
  font-family: montserrat;
font-size: 13px;
}
 
/*----- Responsive -----*/
@media screen and (max-width: 1150px) {
    
}
 

 
@media screen and (max-width: 860px) {

.one-half {width: 70%;}
.top_menu ul li{margin:0;}

    .nav-menu {
        position:relative;
        display:inline-block;
		margin-left: 20px;
    }
 
    .nav-menu ul.active {
        display:none;
    }
 
    .nav-menu ul {
          width: 100%;
  min-width: 290px;
  position: absolute;
  top: 135%;
  left: 0px;
  padding: 10px 18px;
  box-shadow: 0px 1px 1px rgba(0,0,0,0.15);
  border-radius: 3px;
  background: #303030;
  display: inline-block;
    }
 
    .nav-menu ul:after {
        width:0px;
        height:0px;
        position:absolute;
        top:0%;
        left:22px;
        content:&#39;&#39;;
        transform:translate(0%, -100%);
		border-top-color:transparent;
        border-left:7px solid transparent;
        border-right:7px solid transparent;
        border-bottom:7px solid #303030;
    }
 
    .nav-menu li {
         margin: 12px 0px;
  padding: 0;
  float: none;
  display: block;
    }
 
    .nav-menu ul li a {
        display:block;
		color: #eee;
		padding:0;
    }
 
    .toggle-nav {
        padding: 3px 9px;
		margin-left: 10px;
  position: relative;
  top: 4px;
  float: left;
  display: inline-block;
  box-shadow: 0px 1px 1px rgba(0,0,0,0.15);
  border-radius: 3px;
  background: #303030;
  text-shadow: 0px 1px 0px rgba(0,0,0,0.5);
  color: #777;
  font-size: 22px;
  transition: color linear 0.15s;
    }
 
    .toggle-nav:focus,.toggle-nav:hover, .toggle-nav, .toggle-nav.active {
        text-decoration:none;
        color:#F0BF3E;
    }
 
}


</style>

<style id='owl-carousel' type='text/css'>
/* 
 *  Core Owl Carousel CSS File
 *  v1.3.3
 */

/* clearfix */
.owl-carousel .owl-wrapper:after{content:&quot;:.&quot;display:block;clear:both;visibility:hidden;line-height:0;height:0}.owl-carousel{display:none;position:relative;width:100%;-ms-touch-action:pan-y}.owl-carousel .owl-wrapper{display:none;position:relative}.owl-carousel .owl-wrapper-outer{overflow:hidden;position:relative;width:100%}.owl-carousel .owl-wrapper-outer.autoHeight{-webkit-transition:height 500ms ease-in-out;-moz-transition:height 500ms ease-in-out;-ms-transition:height 500ms ease-in-out;-o-transition:height 500ms ease-in-out;transition:height 500ms ease-in-out}.owl-carousel .owl-item{float:left;margin-right:10px;}.owl-controls .owl-buttons div,.owl-controls .owl-page{cursor:pointer}.owl-controls{-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-tap-highlight-color:transparent}.grabbing{cursor:url(../images/grabbing.png) 8 8,move}.owl-carousel .owl-item,.owl-carousel .owl-wrapper{-webkit-backface-visibility:hidden;-moz-backface-visibility:hidden;-ms-backface-visibility:hidden;-webkit-transform:translate3d(0,0,0);-moz-transform:translate3d(0,0,0);-ms-transform:translate3d(0,0,0)}.owl-theme .owl-controls{margin-top:0px;text-align:center;position:absolute;bottom:0px;right:10px;}
.owl-theme .owl-controls .owl-buttons div {color: #060606;display: inline-block;zoom: 1;margin: 0px;padding:0;font-size: 28px;width: 40px;height:40px;-webkit-border-radius: 30px;
-moz-border-radius: 30px;border-radius: 30px;background: #FFFFFF;filter: Alpha(Opacity=50);
opacity: .9;}.owl-theme .owl-controls.clickable .owl-buttons div:hover{filter:Alpha(Opacity=100);opacity:1;text-decoration:none}.owl-theme .owl-controls .owl-page{display:inline-block;zoom:1}.owl-theme .owl-controls .owl-page span{display:block;width:12px;height:12px;margin:5px 3px;filter:Alpha(Opacity=95);opacity:.95;-webkit-border-radius:20px;-moz-border-radius:20px;border-radius:20px;background:#fff;}.owl-theme .owl-controls .owl-page.active span{filter:Alpha(Opacity=100);opacity:1;border:3px solid rgba(0,0,0,.6);}.owl-theme .owl-controls.clickable .owl-page:hover span{filter:Alpha(Opacity=100);opacity:1}.owl-theme .owl-controls .owl-page span.owl-numbers{height:auto;width:auto;color:#FFF;padding:2px 10px;font-size:12px;-webkit-border-radius:30px;-moz-border-radius:30px;border-radius:30px}@-webkit-keyframes preloader{0%{transform:translateY(0) scaleX(1.6);-webkit-transform:translateY(0) scaleX(1.6);-ms-transform:translateY(0) scaleX(1.6)}33%{transform:translateY(0) scaleX(1) scaleY(1.3);-webkit-transform:translateY(0) scaleX(1) scaleY(1.3);-ms-transform:translateY(0) scaleX(1) scaleY(1.3)}100%{transform:translateY(-150px) scaleX(1) scaleY(1.1);-webkit-transform:translateY(-150px) scaleX(1) scaleY(1.1);-ms-transform:translateY(-150px) scaleX(1) scaleY(1.1)}}@keyframes preloader{0%{transform:translateY(0) scaleX(1.6);-webkit-transform:translateY(0) scaleX(1.6);-ms-transform:translateY(0) scaleX(1.6)}33%{transform:translateY(0) scaleX(1) scaleY(1.3);-webkit-transform:translateY(0) scaleX(1) scaleY(1.3);-ms-transform:translateY(0) scaleX(1) scaleY(1.3)}100%{transform:translateY(-150px) scaleX(1) scaleY(1.1);-webkit-transform:translateY(-150px) scaleX(1) scaleY(1.1);-ms-transform:translateY(-150px) scaleX(1) scaleY(1.1)}}.owl-item.loading{width:100%;height:auto}.owl-item.loading:after{content:&#39;&#39;position:absolute;margin-top:50%;left:calc(50% - 16px);height:32px;width:32px;background:#D75752;border-radius:50%;-moz-border-radius:50%;-webkit-border-radius:50%;-webkit-animation:preloader 400ms ease-out;animation:preloader 400ms ease-out;animation-iteration-count:infinite;animation-direction:alternate;-webkit-animation-iteration-count:infinite;-webkit-animation-direction:alternate}.owl-wrapper-outer {max-height: 565px;}
.owl_carouselle .owl-controls .owl-page span {width: 13px;height: 13px;}.owl_carouselle .owl-controls .owl-page.active span {border:0;background:$(primary.background.color)}.owl_carouselle .owl-controls .owl-page span{background:#fff;}.owl_carouselle .owl-controls .owl-page span {background: #fff;}.owl_carouselle .owl-controls {text-align: center;position: static;width: 100%;height: 0;}.owl-theme .owl-controls .owl-buttons div {position: absolute;right: 5px;top: 45%;}.owl-theme .owl-controls .owl-buttons div.owl-next {right: auto;left: 2px;}

</style>

<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js' type='text/javascript'/>


<script type='text/javascript'>
 //<![CDATA[

// Featured posts

cat1 = 'featured';

cat2 = 'slider'


imgr = new Array();
imgr[0] = "http://4.bp.blogspot.com/--mxSeYGUGrM/VODW0yz13KI/AAAAAAAABBY/_vMjhXRcdDo/s700/style1.png";
showRandomImg = true;
aBold = true;
numposts1 = 7;
numposts2 = 12
function recentposts1(json){j=showRandomImg?Math.floor((imgr.length+1)*Math.random()):0;img=new Array;if(numposts1<=json.feed.entry.length)maxpost=numposts1;else maxpost=json.feed.entry.length;document.write('<div class="blog_featured_posts" >');for(var i=0;i<maxpost;i++){var entry=json.feed.entry[i];var posttitle=entry.title.$t;var tag=entry.category[0].term;var pcm;var posturl;var cropsize=400;if(i==json.feed.entry.length)break;for(var k=0;k<entry.link.length;k++)
if(entry.link[k].rel=="alternate"){posturl=entry.link[k].href;break}
for(var k=0;k<entry.link.length;k++)
if(entry.link[k].rel=="replies"&&entry.link[k].type=="text/html"){pcm=entry.link[k].title.split(" ")[0];break}
if("content"in entry)var postcontent=entry.content.$t;else if("summary"in entry)var postcontent=entry.summary.$t;else var postcontent="";postdate=entry.published.$t;if(j>imgr.length-1)j=0;img[i]=imgr[j];s=postcontent;a=s.indexOf("<img");b=s.indexOf('src="',a);c=s.indexOf('"',b+5);d=s.substr(b+5,c-b-5);if(a!=-1&&(b!=-1&&(c!=-1&&d!="")))img[i]=d;var month=[1,2,3,4,5,6,7,8,9,10,11,12];var month2=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];var day=postdate.split("-")[2].substring(0,2);var m=postdate.split("-")[1];var y=postdate.split("-")[0];for(var u2=0;u2<month.length;u2++)
if(parseInt(m)==month[u2]){m=month2[u2];break}
var daystr=day+" "+m+" "+y;var trtd='<div class="blog_featured_post"><a class="feat_link" href="'+posturl+'"><img class="img_fade" src="'+img[i]+'"></img><div class="feat-img" style="background-image:url('+img[i]+');"></div></a><div class="feat-info-wrapper"><span class="entry-title"><a href="'+posturl+'" rel="bookmark">'+posttitle+'</a></span><a href="'+posturl+'" class="featured_rd_mre">Read More <i class="fa fa-angle-right"></i></a></div></div>';document.write(trtd)
j++}
document.write('</div>')};
function showrecentposts1(json){j=showRandomImg?Math.floor((imgr.length+1)*Math.random()):0;img=new Array;if(numposts1<=json.feed.entry.length)maxpost=numposts2;else maxpost=json.feed.entry.length;document.write('<div class="owl_carouselle" style="display:none;">');for(var i=0;i<maxpost;i++){var entry=json.feed.entry[i];var posttitle=entry.title.$t;var pcm;var tag_name=entry.category[0].term;var posturl;if(i==json.feed.entry.length)break;for(var k=0;k<entry.link.length;k++)
if(entry.link[k].rel=="alternate"){posturl=entry.link[k].href;break}
if("content"in entry)var postcontent=entry.content.$t;else if("summary"in entry)var postcontent=entry.summary.$t;else var postcontent="";if(j>imgr.length-1)j=0;img[i]=imgr[j];s=postcontent;a=s.indexOf("<img");b=s.indexOf('src="',a);c=s.indexOf('"',b+5);d=s.substr(b+5,c-b-5);if(a!=-1&&(b!=-1&&(c!=-1&&d!="")))img[i]=d;var trtd='<figure class="slider__item clearfix"><figcaption class="slide__caption"><div class="post__description"><p><a href="/search/labels/'+tag_name+'?max-results=5" rel="tag">'+tag_name+'</a></p><h2><a href="'+posturl+'">'+posttitle+'</a></h2></div></figcaption><div class="lazyOwl img__container" style="background-image:url('+img[i]+');display: block;"></div></figure>';document.write(trtd);j++}
document.write('</div>');};


//]]>
</script>



<script type='text/javascript'>
/*<![CDATA[*/
// JavaScript Document
var _0xfc31=["\x53\x20\x31\x34\x28\x65\x2C\x74\x29\x7B\x6C\x28\x65\x2E\x38\x28\x22\x3C\x22\x29\x21\x3D\x2D\x31\x29\x7B\x36\x20\x6E\x3D\x65\x2E\x31\x6C\x28\x22\x3C\x22\x29\x3B\x31\x6B\x28\x36\x20\x72\x3D\x30\x3B\x72\x3C\x6E\x2E\x4D\x3B\x72\x2B\x2B\x29\x7B\x6C\x28\x6E\x5B\x72\x5D\x2E\x38\x28\x22\x3E\x22\x29\x21\x3D\x2D\x31\x29\x7B\x6E\x5B\x72\x5D\x3D\x6E\x5B\x72\x5D\x2E\x77\x28\x6E\x5B\x72\x5D\x2E\x38\x28\x22\x3E\x22\x29\x2B\x31\x2C\x6E\x5B\x72\x5D\x2E\x4D\x29\x7D\x7D\x65\x3D\x6E\x2E\x31\x6A\x28\x22\x22\x29\x7D\x74\x3D\x74\x3C\x65\x2E\x4D\x2D\x31\x3F\x74\x3A\x65\x2E\x4D\x2D\x32\x3B\x31\x69\x28\x65\x2E\x31\x6D\x28\x74\x2D\x31\x29\x21\x3D\x22\x20\x22\x26\x26\x65\x2E\x38\x28\x22\x20\x22\x2C\x74\x29\x21\x3D\x2D\x31\x29\x74\x2B\x2B\x3B\x65\x3D\x65\x2E\x77\x28\x30\x2C\x74\x2D\x31\x29\x3B\x31\x6E\x20\x65\x2B\x22\x22\x7D\x53\x20\x31\x72\x28\x65\x2C\x74\x2C\x4C\x2C\x51\x2C\x31\x33\x2C\x52\x29\x7B\x36\x20\x72\x3D\x55\x2E\x54\x28\x65\x29\x3B\x36\x20\x4C\x3D\x4C\x3B\x36\x20\x51\x3D\x51\x3B\x36\x20\x73\x3D\x22\x22\x3B\x36\x20\x6F\x3D\x72\x2E\x58\x28\x22\x4F\x22\x29\x3B\x36\x20\x75\x3D\x72\x2E\x58\x28\x22\x68\x22\x29\x3B\x36\x20\x61\x3D\x31\x70\x3B\x36\x20\x70\x3D\x22\x22\x3B\x6C\x28\x6F\x2E\x4D\x3E\x3D\x31\x29\x7B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x31\x68\x22\x3E\x3C\x61\x20\x59\x3D\x22\x22\x20\x48\x3D\x22\x27\x2B\x74\x2B\x27\x22\x3E\x3C\x4F\x20\x62\x3D\x22\x27\x2B\x6F\x5B\x30\x5D\x2E\x62\x2E\x31\x73\x28\x2F\x73\x5C\x42\x5C\x64\x7B\x32\x2C\x34\x7D\x2F\x2C\x27\x73\x27\x2B\x31\x66\x29\x2B\x27\x22\x20\x35\x3D\x22\x4F\x2D\x31\x36\x22\x2F\x3E\x3C\x2F\x61\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x47\x7D\x6C\x28\x72\x2E\x71\x2E\x38\x28\x22\x31\x38\x22\x29\x21\x3D\x2D\x31\x29\x7B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x7A\x22\x3E\x3C\x61\x20\x59\x3D\x22\x22\x20\x48\x3D\x22\x27\x2B\x74\x2B\x27\x22\x3E\x3C\x4F\x20\x31\x67\x3D\x22\x22\x20\x35\x3D\x22\x43\x22\x20\x62\x3D\x22\x27\x2B\x6F\x5B\x30\x5D\x2E\x62\x2B\x27\x22\x20\x41\x3D\x22\x27\x2B\x46\x2B\x27\x66\x22\x20\x44\x3D\x22\x27\x2B\x45\x2B\x27\x66\x22\x2F\x3E\x3C\x2F\x61\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x5A\x7D\x6C\x28\x72\x2E\x71\x2E\x38\x28\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x76\x2F\x22\x29\x21\x3D\x2D\x31\x29\x7B\x36\x20\x76\x3D\x75\x5B\x30\x5D\x2E\x62\x3B\x36\x20\x6D\x3D\x76\x2E\x77\x28\x76\x2E\x38\x28\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x76\x2F\x22\x29\x2B\x31\x30\x29\x3B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x7A\x22\x3E\x3C\x61\x20\x48\x3D\x22\x27\x2B\x74\x2B\x27\x22\x3E\x3C\x68\x20\x35\x3D\x22\x43\x22\x20\x62\x3D\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x27\x2B\x6D\x2B\x27\x22\x20\x41\x3D\x22\x27\x2B\x46\x2B\x27\x66\x22\x20\x44\x3D\x22\x27\x2B\x45\x2B\x27\x66\x22\x3E\x3C\x2F\x68\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x47\x7D\x6C\x28\x72\x2E\x71\x2E\x38\x28\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x22\x29\x21\x3D\x2D\x31\x29\x7B\x36\x20\x76\x3D\x75\x5B\x30\x5D\x2E\x62\x3B\x36\x20\x6D\x3D\x76\x2E\x77\x28\x76\x2E\x38\x28\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x22\x29\x2B\x31\x61\x29\x3B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x7A\x20\x39\x22\x3E\x3C\x68\x20\x35\x3D\x22\x43\x22\x20\x62\x3D\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x27\x2B\x6D\x2B\x27\x22\x20\x41\x3D\x22\x27\x2B\x46\x2B\x27\x66\x22\x20\x44\x3D\x22\x27\x2B\x45\x2B\x27\x66\x22\x3E\x3C\x2F\x68\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x47\x7D\x6C\x28\x72\x2E\x71\x2E\x38\x28\x22\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x22\x29\x21\x3D\x2D\x31\x29\x7B\x36\x20\x76\x3D\x75\x5B\x30\x5D\x2E\x62\x3B\x36\x20\x6D\x3D\x76\x2E\x77\x28\x76\x2E\x38\x28\x22\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x22\x29\x2B\x31\x65\x29\x3B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x7A\x20\x39\x22\x3E\x3C\x68\x20\x35\x3D\x22\x43\x22\x20\x62\x3D\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x27\x2B\x6D\x2B\x27\x22\x20\x41\x3D\x22\x27\x2B\x46\x2B\x27\x66\x22\x20\x44\x3D\x22\x27\x2B\x45\x2B\x27\x66\x22\x3E\x3C\x2F\x68\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x47\x7D\x6C\x28\x72\x2E\x71\x2E\x38\x28\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2D\x4E\x2E\x37\x2F\x6B\x2F\x22\x29\x21\x3D\x2D\x31\x29\x7B\x36\x20\x76\x3D\x75\x5B\x30\x5D\x2E\x62\x3B\x36\x20\x6D\x3D\x76\x2E\x77\x28\x76\x2E\x38\x28\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2D\x4E\x2E\x37\x2F\x6B\x2F\x22\x29\x2B\x31\x64\x29\x3B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x7A\x20\x39\x22\x20\x3E\x3C\x68\x20\x35\x3D\x22\x43\x22\x20\x62\x3D\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x27\x2B\x6D\x2B\x27\x22\x20\x41\x3D\x22\x27\x2B\x46\x2B\x27\x66\x22\x20\x44\x3D\x22\x27\x2B\x45\x2B\x27\x66\x22\x3E\x3C\x2F\x68\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x5A\x7D\x6C\x28\x72\x2E\x71\x2E\x38\x28\x22\x2F\x2F\x63\x2E\x39\x2D\x4E\x2E\x37\x2F\x6B\x2F\x22\x29\x21\x3D\x2D\x31\x29\x7B\x36\x20\x76\x3D\x75\x5B\x30\x5D\x2E\x62\x3B\x36\x20\x6D\x3D\x76\x2E\x77\x28\x76\x2E\x38\x28\x22\x2F\x2F\x63\x2E\x39\x2D\x4E\x2E\x37\x2F\x6B\x2F\x22\x29\x2B\x31\x62\x29\x3B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x7A\x20\x39\x22\x3E\x3C\x68\x20\x35\x3D\x22\x43\x22\x20\x62\x3D\x22\x6A\x3A\x2F\x2F\x63\x2E\x39\x2E\x37\x2F\x6B\x2F\x27\x2B\x6D\x2B\x27\x22\x20\x41\x3D\x22\x27\x2B\x46\x2B\x27\x66\x22\x20\x44\x3D\x22\x27\x2B\x45\x2B\x27\x66\x22\x3E\x3C\x2F\x68\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x47\x7D\x6C\x28\x72\x2E\x71\x2E\x38\x28\x22\x6A\x3A\x2F\x2F\x4B\x2E\x79\x2E\x37\x2F\x49\x2F\x22\x29\x21\x3D\x2D\x31\x29\x7B\x36\x20\x76\x3D\x75\x5B\x30\x5D\x2E\x62\x3B\x36\x20\x6D\x3D\x76\x2E\x77\x28\x76\x2E\x38\x28\x22\x6A\x3A\x2F\x2F\x4B\x2E\x79\x2E\x37\x2F\x49\x2F\x22\x29\x2B\x31\x6F\x29\x3B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x7A\x20\x79\x22\x3E\x3C\x68\x20\x35\x3D\x22\x43\x22\x20\x62\x3D\x22\x2F\x2F\x4B\x2E\x79\x2E\x37\x2F\x49\x2F\x27\x2B\x6D\x2B\x27\x22\x20\x41\x3D\x22\x27\x2B\x46\x2B\x27\x66\x22\x20\x44\x3D\x22\x27\x2B\x45\x2B\x27\x66\x22\x20\x3E\x3C\x2F\x68\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x47\x7D\x6C\x28\x72\x2E\x71\x2E\x38\x28\x22\x2F\x2F\x4B\x2E\x79\x2E\x37\x2F\x49\x2F\x22\x29\x21\x3D\x2D\x31\x29\x7B\x36\x20\x76\x3D\x75\x5B\x30\x5D\x2E\x62\x3B\x36\x20\x6D\x3D\x76\x2E\x77\x28\x76\x2E\x38\x28\x22\x2F\x2F\x4B\x2E\x79\x2E\x37\x2F\x49\x2F\x22\x29\x2B\x31\x30\x29\x3B\x73\x3D\x27\x3C\x33\x20\x35\x3D\x22\x7A\x20\x79\x22\x3E\x3C\x68\x20\x35\x3D\x22\x43\x22\x20\x62\x3D\x22\x2F\x2F\x4B\x2E\x79\x2E\x37\x2F\x49\x2F\x27\x2B\x6D\x2B\x27\x22\x20\x41\x3D\x22\x27\x2B\x46\x2B\x27\x66\x22\x20\x44\x3D\x22\x27\x2B\x45\x2B\x27\x66\x22\x20\x3E\x3C\x2F\x68\x3E\x3C\x2F\x33\x3E\x27\x3B\x61\x3D\x47\x7D\x36\x20\x67\x3D\x27\x3C\x33\x20\x35\x3D\x22\x31\x41\x22\x3E\x27\x2B\x73\x2B\x27\x3C\x33\x20\x35\x3D\x22\x31\x45\x22\x3E\x3C\x33\x20\x35\x3D\x22\x31\x44\x22\x3E\x3C\x33\x20\x35\x3D\x22\x31\x43\x22\x3E\x3C\x4A\x20\x35\x3D\x22\x50\x20\x31\x42\x22\x3E\x3C\x69\x20\x35\x3D\x22\x31\x31\x20\x31\x31\x2D\x31\x63\x22\x3E\x3C\x2F\x69\x3E\x27\x2B\x31\x33\x2B\x27\x3C\x2F\x4A\x3E\x3C\x31\x35\x3E\x3C\x61\x20\x48\x3D\x22\x27\x2B\x74\x2B\x27\x22\x3E\x27\x2B\x78\x2B\x27\x3C\x2F\x61\x3E\x3C\x2F\x31\x35\x3E\x3C\x33\x20\x35\x3D\x22\x50\x20\x31\x47\x22\x3E\x3C\x4A\x20\x35\x3D\x22\x52\x22\x3E\x31\x77\x20\x27\x2B\x52\x2B\x27\x3C\x2F\x4A\x3E\x3C\x4A\x20\x35\x3D\x22\x4C\x22\x3E\x31\x79\x20\x27\x2B\x4C\x2B\x27\x3C\x2F\x4A\x3E\x3C\x2F\x33\x3E\x3C\x2F\x33\x3E\x3C\x33\x20\x35\x3D\x22\x31\x46\x20\x31\x4A\x22\x3E\x3C\x70\x3E\x27\x2B\x31\x34\x28\x72\x2E\x71\x2C\x61\x29\x2B\x27\x3C\x2F\x70\x3E\x3C\x33\x20\x35\x3D\x22\x50\x2D\x31\x39\x20\x31\x37\x20\x50\x22\x3E\x3C\x61\x20\x48\x3D\x22\x27\x2B\x74\x2B\x27\x22\x3E\x27\x2B\x31\x71\x2B\x27\x3C\x2F\x61\x3E\x3C\x2F\x33\x3E\x3C\x2F\x33\x3E\x3C\x2F\x33\x3E\x3C\x2F\x33\x3E\x3C\x2F\x33\x3E\x27\x3B\x72\x2E\x71\x3D\x67\x7D\x56\x2E\x31\x7A\x3D\x53\x28\x29\x7B\x36\x20\x65\x3D\x55\x2E\x54\x28\x22\x31\x76\x22\x29\x3B\x6C\x28\x65\x3D\x3D\x31\x75\x29\x7B\x56\x2E\x31\x78\x2E\x48\x3D\x22\x6A\x3A\x2F\x2F\x63\x2E\x31\x32\x2E\x37\x22\x7D\x65\x2E\x57\x28\x22\x48\x22\x2C\x22\x6A\x3A\x2F\x2F\x63\x2E\x31\x32\x2E\x37\x2F\x22\x29\x3B\x65\x2E\x57\x28\x22\x31\x48\x22\x2C\x22\x31\x49\x22\x29\x3B\x65\x2E\x71\x3D\x22\x31\x74\x2E\x37\x22\x7D","\x7C","\x73\x70\x6C\x69\x74","\x7C\x7C\x7C\x64\x69\x76\x7C\x7C\x63\x6C\x61\x73\x73\x7C\x76\x61\x72\x7C\x63\x6F\x6D\x7C\x69\x6E\x64\x65\x78\x4F\x66\x7C\x79\x6F\x75\x74\x75\x62\x65\x7C\x7C\x73\x72\x63\x7C\x77\x77\x77\x7C\x7C\x7C\x70\x78\x7C\x7C\x69\x66\x72\x61\x6D\x65\x7C\x7C\x68\x74\x74\x70\x7C\x65\x6D\x62\x65\x64\x7C\x69\x66\x7C\x7C\x7C\x7C\x7C\x69\x6E\x6E\x65\x72\x48\x54\x4D\x4C\x7C\x7C\x7C\x7C\x7C\x7C\x73\x75\x62\x73\x74\x72\x69\x6E\x67\x7C\x7C\x76\x69\x6D\x65\x6F\x7C\x70\x6C\x61\x79\x62\x75\x74\x74\x6F\x6E\x7C\x77\x69\x64\x74\x68\x7C\x7C\x69\x6D\x67\x63\x6F\x6E\x7C\x68\x65\x69\x67\x68\x74\x7C\x74\x68\x68\x7C\x74\x68\x77\x7C\x73\x75\x6D\x6D\x61\x72\x79\x69\x7C\x68\x72\x65\x66\x7C\x76\x69\x64\x65\x6F\x7C\x73\x70\x61\x6E\x7C\x70\x6C\x61\x79\x65\x72\x7C\x64\x61\x74\x65\x7C\x6C\x65\x6E\x67\x74\x68\x7C\x6E\x6F\x63\x6F\x6F\x6B\x69\x65\x7C\x69\x6D\x67\x7C\x6D\x65\x74\x61\x7C\x63\x6F\x6D\x6D\x65\x6E\x74\x7C\x61\x75\x74\x68\x6F\x72\x7C\x66\x75\x6E\x63\x74\x69\x6F\x6E\x7C\x67\x65\x74\x45\x6C\x65\x6D\x65\x6E\x74\x42\x79\x49\x64\x7C\x64\x6F\x63\x75\x6D\x65\x6E\x74\x7C\x77\x69\x6E\x64\x6F\x77\x7C\x73\x65\x74\x41\x74\x74\x72\x69\x62\x75\x74\x65\x7C\x67\x65\x74\x45\x6C\x65\x6D\x65\x6E\x74\x73\x42\x79\x54\x61\x67\x4E\x61\x6D\x65\x7C\x74\x69\x74\x6C\x65\x7C\x73\x75\x6D\x6D\x61\x72\x79\x76\x7C\x32\x35\x7C\x66\x61\x7C\x76\x65\x65\x74\x68\x65\x6D\x65\x73\x7C\x74\x61\x67\x7C\x72\x65\x6D\x6F\x76\x65\x48\x74\x6D\x6C\x54\x61\x67\x7C\x68\x32\x7C\x72\x65\x73\x70\x6F\x6E\x73\x69\x76\x65\x7C\x72\x64\x6D\x72\x65\x7C\x74\x68\x75\x6D\x62\x76\x69\x64\x65\x6F\x7C\x69\x74\x65\x6D\x7C\x32\x39\x7C\x33\x33\x7C\x62\x6F\x6F\x6B\x6D\x61\x72\x6B\x7C\x33\x38\x7C\x32\x34\x7C\x38\x32\x30\x7C\x61\x6C\x74\x7C\x61\x72\x74\x69\x63\x6C\x65\x5F\x69\x6D\x61\x67\x65\x7C\x77\x68\x69\x6C\x65\x7C\x6A\x6F\x69\x6E\x7C\x66\x6F\x72\x7C\x73\x70\x6C\x69\x74\x7C\x63\x68\x61\x72\x41\x74\x7C\x72\x65\x74\x75\x72\x6E\x7C\x33\x30\x7C\x73\x75\x6D\x6D\x61\x72\x79\x5F\x6E\x6F\x69\x6D\x67\x7C\x52\x65\x61\x64\x6D\x6F\x72\x65\x5F\x77\x6F\x72\x64\x7C\x72\x6D\x7C\x72\x65\x70\x6C\x61\x63\x65\x7C\x56\x65\x65\x54\x68\x65\x6D\x65\x73\x7C\x6E\x75\x6C\x6C\x7C\x61\x74\x74\x72\x69\x5F\x62\x75\x74\x69\x6F\x6E\x7C\x62\x79\x7C\x6C\x6F\x63\x61\x74\x69\x6F\x6E\x7C\x6F\x6E\x7C\x6F\x6E\x6C\x6F\x61\x64\x7C\x61\x72\x74\x69\x63\x6C\x65\x5F\x63\x6F\x6E\x74\x61\x69\x6E\x65\x72\x7C\x6D\x65\x74\x61\x5F\x74\x61\x67\x7C\x61\x72\x74\x69\x63\x6C\x65\x5F\x68\x65\x61\x64\x65\x72\x7C\x61\x72\x74\x69\x63\x6C\x65\x5F\x63\x6F\x6E\x74\x7C\x61\x72\x74\x69\x63\x6C\x65\x5F\x69\x6E\x6E\x65\x72\x7C\x61\x72\x74\x69\x63\x6C\x65\x5F\x65\x78\x63\x65\x72\x70\x74\x7C\x70\x6F\x73\x74\x5F\x6D\x65\x74\x61\x7C\x72\x65\x6C\x7C\x64\x6F\x66\x6F\x6C\x6C\x6F\x77\x7C\x63\x6C\x65\x61\x72\x66\x69\x78","","\x66\x72\x6F\x6D\x43\x68\x61\x72\x43\x6F\x64\x65","\x72\x65\x70\x6C\x61\x63\x65","\x5C\x77\x2B","\x5C\x62","\x67"];eval(function(_0xa633x1,_0xa633x2,_0xa633x3,_0xa633x4,_0xa633x5,_0xa633x6){_0xa633x5=function(_0xa633x3){return (_0xa633x3<_0xa633x2?_0xfc31[4]:_0xa633x5(parseInt(_0xa633x3/_0xa633x2)))+((_0xa633x3=_0xa633x3%_0xa633x2)>35?String[_0xfc31[5]](_0xa633x3+29):_0xa633x3.toString(36))};if(!_0xfc31[4][_0xfc31[6]](/^/,String)){while(_0xa633x3--){_0xa633x6[_0xa633x5(_0xa633x3)]=_0xa633x4[_0xa633x3]||_0xa633x5(_0xa633x3)};_0xa633x4=[function(_0xa633x5){return _0xa633x6[_0xa633x5]}];_0xa633x5=function(){return _0xfc31[7]};_0xa633x3=1;};while(_0xa633x3--){if(_0xa633x4[_0xa633x3]){_0xa633x1=_0xa633x1[_0xfc31[6]]( new RegExp(_0xfc31[8]+_0xa633x5(_0xa633x3)+_0xfc31[8],_0xfc31[9]),_0xa633x4[_0xa633x3])}};return _0xa633x1;}(_0xfc31[0],62,108,_0xfc31[3][_0xfc31[2]](_0xfc31[1]),0,{}));
var Readmore_word = "Continue Reading.."; // Append  " Read More " String after post break 
var summary_noimg = 250;
summaryi = 200;
summaryv = 200;
thh = 420;
thw = 674;

jQuery(document).ready(function() {
    jQuery('a.toggle-nav').click(function(e) {
        jQuery(this).toggleClass('active');
        jQuery('.nav-menu ul').toggleClass('active');
 
        e.preventDefault();
    });


});

/*]]>*/</script>


</head>
<!--<body>-->
<body>

<div class='hdr-top'>
<div class='ct-wrapper'>

<div class='row'>
<div class='one-half first '>
<div class='top_menu'>   
<ul>
<li><a href='http://www.wajibbaca.com/p/kontak-kami.html'>Kontak</a></li>
<li><a href='http://www.wajibbaca.com/p/profile.html'>Tentang Kami</a></li>
<li><a href='http://www.wajibbaca.com/p/sitemap.html'>Sitemap</a></li>
</ul>
</div>
</div>


	
<div class='one-half last social-media-container'>

<ul class='header-social visible'>
<li><a href='http://facebook.com'><i class='fa fa-facebook'/></a></li>
<li><a href='http://twitter.com'><i class='fa fa-twitter'/></a></li>
<li><a href='http://instagram.com'><i class='fa fa-instagram'/></a></li>
<li><a href='http://pinterest.com'><i class='fa fa-pinterest'/></a></li>
<li><a href='http://google.com/plus'><i class='fa fa-google-plus'/></a></li>
<li><a href='http://tumblr.com'><i class='fa fa-tumblr'/></a></li>
</ul>

</div>   
</div>
		<div class='clear'/>
  </div>
</div>

                <div class='header-wrapper'>
        <div class='ct-wrapper'>
<div class='row'>
                <b:section class='header' id='header' maxwidgets='1' showaddelement='yes'>
                  <b:widget id='Header1' locked='true' title='WAJIB BACA | Kumpulan Informasi LUCU UNIK dan MENARIK (Header)' type='Header'>
                    <b:includable id='main'>
  <b:if cond='data:useImage'>
    <b:if cond='data:imagePlacement == &quot;BEHIND&quot;'>
      <!--
      Show image as background to text. You can't really calculate the width
      reliably in JS because margins are not taken into account by any of
      clientWidth, offsetWidth or scrollWidth, so we don't force a minimum
      width if the user is using shrink to fit.
      This results in a margin-width's worth of pixels being cropped. If the
      user is not using shrink to fit then we expand the header.
      -->
      <b:if cond='data:mobile'>
          <div id='header-inner'>
            <div class='titlewrapper' style='background: transparent'>
              <h1 class='title' style='background: transparent; border-width: 0px'>
                <b:include name='title'/>
              </h1>
            </div>
            <b:include name='description'/>
          </div>
        <b:else/>
          <div expr:style='&quot;background-image: url(\&quot;&quot; + data:sourceUrl + &quot;\&quot;); &quot;                        + &quot;background-position: &quot;                        + data:backgroundPositionStyleStr + &quot;; &quot;                        + data:widthStyleStr                        + &quot;min-height: &quot; + data:height                        + &quot;_height: &quot; + data:height                        + &quot;background-repeat: no-repeat; &quot;' id='header-inner'>
            <div class='titlewrapper' style='background: transparent'>
              <h1 class='title' style='background: transparent; border-width: 0px'>
                <b:include name='title'/>
              </h1>
            </div>
            <b:include name='description'/>
          </div>
        </b:if>
    <b:else/>
      <!--Show the image only-->
      <div id='header-inner'>
        <a class='logo-img' expr:href='data:blog.homepageUrl' style='display: block'>
          <img expr:alt='data:title' expr:id='data:widget.instanceId + &quot;_headerimg&quot;' expr:src='data:sourceUrl' style='display: block'/>
        </a>
        <!--Show the description-->
        <b:if cond='data:imagePlacement == &quot;BEFORE_DESCRIPTION&quot;'>
          <b:include name='description'/>
        </b:if>
      </div>
    </b:if>
  <b:else/>
    <!--No header image -->
    <div id='header-inner'>
      <div class='titlewrapper'>
        <h1 class='title'>
          <b:include name='title'/>
        </h1>
      </div>
      <b:include name='description'/>
    </div>
  </b:if>
</b:includable>
                    <b:includable id='description'>
  <div class='descriptionwrapper'>
    <p class='description'><span><data:description/></span></p>
  </div>
</b:includable>
                    <b:includable id='title'>
    <a expr:href='data:blog.homepageUrl'><data:title/></a>
</b:includable>
                  </b:widget>
                </b:section>
     
<b:section class='navigation' id='navigation' maxwidgets='1' showaddelement='no'>
  <b:widget id='LinkList1' locked='false' title='Navigation' type='LinkList'>
    <b:includable id='main'>

<div class='widget-content nav-menu'>
<a class='toggle-nav' href='#'>&#9776;</a>

   <ul class='active'>
     <b:loop values='data:links' var='link'>
       <li><a expr:href='data:link.target'><data:link.name/></a></li>
     </b:loop>
   </ul>
  </div>


<div class='search-include'>
<div class='search-button'>
<!--<a href="#search-popup" class="open_s"><i class="icon-search"></i></a>-->
<a class='open_s' href='#search-popup' original-title='Click here to search' rel='tooltip'><i class='fa fa-search'/></a>
</div>
                                
<div class='zoom-anim-dialog popup-search mfp-hide' id='search-popup'>
<div class='form_wrap'>
<form action='/search' id='search_form' method='get'>
<input autocomplete='off' class='ft' id='srch_txt' name='q' placeholder='Search...' type='text'/>
</form>
</div>
</div>
</div>

</b:includable>
  </b:widget>
</b:section>
          </div>
</div>
</div>
<div class='clr'/>

<!--slider section -->
<b:if cond='data:blog.pageType == &quot;index&quot;'>
<b:section class='slider' id='Slider' maxwidgets='1' showaddelement='no'>
  <b:widget id='HTML7' locked='true' title='Slider' type='HTML'>
    <b:includable id='main'>
            
<!-- Slider Container -->
<div class='carouselle3 slider' id='main-slider'>

<script>document.write(&quot;&lt;script src=\&quot;/feeds/posts/default/-/&quot;+cat2+&quot;?max-results=&quot;+numposts1+&quot;&amp;orderby=published&amp;alt=json-in-script&amp;callback=showrecentposts1\&quot;&gt;&lt;\/script&gt;&quot;);</script>

</div>
<!-- Slider Container -->
    
              </b:includable>
  </b:widget>
</b:section>
<div class='clr'/>
</b:if>



        <div class='ct-wrapper'>
           <div class='outer-wrapper'>


             <div class='main-wrapper'>


                    <b:section class='content' id='content' showaddelement='no'>
                      <b:widget id='Blog1' locked='true' title='Blog Posts' type='Blog'>
                        <b:includable id='main' var='top'>
  <b:if cond='data:mobile == &quot;false&quot;'>

    <!-- posts -->
    <div class='blog-posts hfeed'>

      <b:include data='top' name='status-message'/>

      <data:defaultAdStart/>
      <b:loop values='data:posts' var='post'>
        <div class='post-outer'>
        <b:include data='post' name='post'/>
        <b:if cond='data:blog.pageType == &quot;item&quot;'>
          <!-- navigation -->
          <b:include name='nextprev'/>
        </b:if>
        <b:if cond='data:blog.pageType == &quot;static_page&quot;'>
          <b:include data='post' name='comment_picker'/>
        </b:if>
        <b:if cond='data:blog.pageType == &quot;item&quot;'>
          <b:include data='post' name='comment_picker'/>
        </b:if>
        </div>
        <b:if cond='data:post.includeAd'>
          <b:if cond='data:post.isFirstPost'>
            <data:defaultAdEnd/>
          <b:else/>
            <data:adEnd/>
          </b:if>
          <div class='inline-ad'>
            <data:adCode/>
          </div>
          <data:adStart/>
        </b:if>
      </b:loop>
      <data:adEnd/>
    </div>

    <b:if cond='data:blog.pageType != &quot;item&quot;'>
    <!-- navigation -->
    <b:include name='nextprev'/>
    </b:if>


    <b:if cond='data:top.showStars'>
      <script src='//www.google.com/jsapi' type='text/javascript'/>
      <script type='text/javascript'>
        google.load(&quot;annotations&quot;, &quot;1&quot;, {&quot;locale&quot;: &quot;<data:top.languageCode/>&quot;});
        function initialize() {
          google.annotations.setApplicationId(<data:top.blogspotReviews/>);
          google.annotations.createAll();
          google.annotations.fetch();
        }
        google.setOnLoadCallback(initialize);
      </script>
    </b:if>

  <b:else/>
    <b:include name='mobile-main'/>
  </b:if>

  <b:if cond='data:top.showDummy'>
    <data:top.dummyBootstrap/>
  </b:if>

</b:includable>
                        <b:includable id='backlinkDeleteIcon' var='backlink'>
  <span expr:class='&quot;item-control &quot; + data:backlink.adminClass'>
    <a expr:href='data:backlink.deleteUrl' expr:title='data:top.deleteBacklinkMsg'>
      <img src='//www.blogger.com/img/icon_delete13.gif'/>
    </a>
  </span>
</b:includable>
                        <b:includable id='backlinks' var='post'>
  <a name='links'/><h4><data:post.backlinksLabel/></h4>
  <b:if cond='data:post.numBacklinks != 0'>
    <dl class='comments-block' id='comments-block'>
      <b:loop values='data:post.backlinks' var='backlink'>
        <div class='collapsed-backlink backlink-control'>
          <dt class='comment-title'>
            <span class='backlink-toggle-zippy'>&#160;</span>
            <a expr:href='data:backlink.url' rel='nofollow'><data:backlink.title/></a>
            <b:include data='backlink' name='backlinkDeleteIcon'/>
          </dt>
          <dd class='comment-body collapseable'>
            <data:backlink.snippet/>
          </dd>
          <dd class='comment-footer collapseable'>
            <span class='comment-author'><data:post.authorLabel/> <data:backlink.author/></span>
            <span class='comment-timestamp'><data:post.timestampLabel/> <data:backlink.timestamp/></span>
          </dd>
        </div>
      </b:loop>
    </dl>
  </b:if>
  <p class='comment-footer'>
    <a class='comment-link' expr:href='data:post.createLinkUrl' expr:id='data:widget.instanceId + &quot;_backlinks-create-link&quot;' target='_blank'><data:post.createLinkLabel/></a>
  </p>
</b:includable>
                        <b:includable id='comment-form' var='post'>
  <div class='comment-form'>
    <a name='comment-form'/>
    <b:if cond='data:mobile'>
      <h4 id='comment-post-message'>
        <a expr:id='data:widget.instanceId + &quot;_comment-editor-toggle-link&quot;' href='javascript:void(0)'><data:postCommentMsg/></a></h4>
      <p><data:blogCommentMessage/></p>
      <data:blogTeamBlogMessage/>
      <a expr:href='data:post.commentFormIframeSrc' id='comment-editor-src'/>
      <iframe allowtransparency='true' class='blogger-iframe-colorize blogger-comment-from-post' frameborder='0' height='410' id='comment-editor' name='comment-editor' src='' style='display: none' width='100%'/>
    <b:else/>
      <h4 id='comment-post-message'><data:postCommentMsg/></h4>
      <p><data:blogCommentMessage/></p>
      <data:blogTeamBlogMessage/>
      <a expr:href='data:post.commentFormIframeSrc' id='comment-editor-src'/>
      <iframe allowtransparency='true' class='blogger-iframe-colorize blogger-comment-from-post' frameborder='0' height='410' id='comment-editor' name='comment-editor' src='' width='100%'/>
    </b:if>
    <data:post.friendConnectJs/>
    <data:post.cmtfpIframe/>
    <script type='text/javascript'>
      BLOG_CMT_createIframe(&#39;<data:post.appRpcRelayPath/>&#39;, &#39;<data:post.communityId/>&#39;);
    </script>
  </div>
</b:includable>
                        <b:includable id='commentDeleteIcon' var='comment'>
  <span expr:class='&quot;item-control &quot; + data:comment.adminClass'>
    <b:if cond='data:showCmtPopup'>
      <div class='goog-toggle-button'>
        <div class='goog-inline-block comment-action-icon'/>
      </div>
    <b:else/>
      <a class='comment-delete' expr:href='data:comment.deleteUrl' expr:title='data:top.deleteCommentMsg'>
        <img src='//www.blogger.com/img/icon_delete13.gif'/>
      </a>
    </b:if>
  </span>
</b:includable>
                        <b:includable id='comment_count_picker' var='post'>
  <b:if cond='data:post.commentSource == 1'>
    <span class='cmt_count_iframe_holder' expr:data-count='data:post.numComments' expr:data-onclick='data:post.addCommentOnclick' expr:data-post-url='data:post.url' expr:data-url='data:post.canonicalUrl'>
    </span>
  <b:else/>
    <a class='comment-link' expr:href='data:post.addCommentUrl' expr:onclick='data:post.addCommentOnclick'>
      <data:post.commentLabelFull/>:
    </a>
  </b:if>
</b:includable>
                        <b:includable id='comment_picker' var='post'>
  <b:if cond='data:post.commentSource == 1'>
    <b:include data='post' name='iframe_comments'/>
  <b:else/>
    <b:if cond='data:post.showThreadedComments'>
      <b:include data='post' name='threaded_comments'/>
    <b:else/>
      <b:include data='post' name='comments'/>
    </b:if>
  </b:if>
</b:includable>
                        <b:includable id='comments' var='post'>
  <div class='comments' id='comments'>
    <a name='comments'/>
    <b:if cond='data:post.allowComments'>
      <h4>
        <b:if cond='data:post.numComments == 1'>
          1 <data:commentLabel/>:
        <b:else/>
          <data:post.numComments/> <data:commentLabelPlural/>:
        </b:if>
      </h4>

      <b:if cond='data:post.commentPagingRequired'>
        <span class='paging-control-container'>
          <a expr:class='data:post.oldLinkClass' expr:href='data:post.oldestLinkUrl'><data:post.oldestLinkText/></a>
          &#160;
          <a expr:class='data:post.oldLinkClass' expr:href='data:post.olderLinkUrl'><data:post.olderLinkText/></a>
          &#160;
          <data:post.commentRangeText/>
          &#160;
          <a expr:class='data:post.newLinkClass' expr:href='data:post.newerLinkUrl'><data:post.newerLinkText/></a>
          &#160;
          <a expr:class='data:post.newLinkClass' expr:href='data:post.newestLinkUrl'><data:post.newestLinkText/></a>
        </span>
      </b:if>

      <div expr:id='data:widget.instanceId + &quot;_comments-block-wrapper&quot;'>
        <dl expr:class='data:post.avatarIndentClass' id='comments-block'>
          <b:loop values='data:post.comments' var='comment'>
            <dt expr:class='&quot;comment-author &quot; + data:comment.authorClass' expr:id='data:comment.anchorName'>
              <b:if cond='data:comment.favicon'>
                <img expr:src='data:comment.favicon' height='16px' style='margin-bottom:-2px;' width='16px'/>
              </b:if>
              <a expr:name='data:comment.anchorName'/>
              <b:if cond='data:blog.enabledCommentProfileImages'>
                <data:comment.authorAvatarImage/>
              </b:if>
              <b:if cond='data:comment.authorUrl'>
                <a expr:href='data:comment.authorUrl' rel='nofollow'><data:comment.author/></a>
              <b:else/>
                <data:comment.author/>
              </b:if>
              <data:commentPostedByMsg/>
            </dt>
            <dd class='comment-body' expr:id='data:widget.instanceId + data:comment.cmtBodyIdPostfix'>
              <b:if cond='data:comment.isDeleted'>
                <span class='deleted-comment'><data:comment.body/></span>
              <b:else/>
                <p>
                  <data:comment.body/>
                </p>
              </b:if>
            </dd>
            <dd class='comment-footer'>
              <span class='comment-timestamp'>
                <a expr:href='data:comment.url' title='comment permalink'>
                  <data:comment.timestamp/>
                </a>
                <b:include data='comment' name='commentDeleteIcon'/>
              </span>
            </dd>
          </b:loop>
        </dl>
      </div>

      <b:if cond='data:post.commentPagingRequired'>
        <span class='paging-control-container'>
          <a expr:class='data:post.oldLinkClass' expr:href='data:post.oldestLinkUrl'>
            <data:post.oldestLinkText/>
          </a>
          <a expr:class='data:post.oldLinkClass' expr:href='data:post.olderLinkUrl'>
            <data:post.olderLinkText/>
          </a>
          &#160;
          <data:post.commentRangeText/>
          &#160;
          <a expr:class='data:post.newLinkClass' expr:href='data:post.newerLinkUrl'>
            <data:post.newerLinkText/>
          </a>
          <a expr:class='data:post.newLinkClass' expr:href='data:post.newestLinkUrl'>
            <data:post.newestLinkText/>
          </a>
        </span>
      </b:if>

      <p class='comment-footer'>
        <b:if cond='data:post.embedCommentForm'>
          <b:if cond='data:post.allowNewComments'>
            <b:include data='post' name='comment-form'/>
          <b:else/>
            <data:post.noNewCommentsText/>
          </b:if>
        <b:else/>
          <b:if cond='data:post.allowComments'>
            <a expr:href='data:post.addCommentUrl' expr:onclick='data:post.addCommentOnclick'><data:postCommentMsg/></a>
          </b:if>
        </b:if>

      </p>
    </b:if>
    <b:if cond='data:showCmtPopup'>
      <div id='comment-popup'>
        <iframe allowtransparency='true' frameborder='0' id='comment-actions' name='comment-actions' scrolling='no'>
        </iframe>
      </div>
    </b:if>

    <div id='backlinks-container'>
    <div expr:id='data:widget.instanceId + &quot;_backlinks-container&quot;'>
       <b:if cond='data:post.showBacklinks'>
         <b:include data='post' name='backlinks'/>
       </b:if>
    </div>
    </div>
  </div>
</b:includable>
                        <b:includable id='feedLinks'>
  <b:if cond='data:blog.pageType != &quot;item&quot;'> <!-- Blog feed links -->
    <b:if cond='data:feedLinks'>
      <div class='blog-feeds'>
        <b:include data='feedLinks' name='feedLinksBody'/>
      </div>
    </b:if>

    <b:else/> <!--Post feed links -->
    <div class='post-feeds'>
      <b:loop values='data:posts' var='post'>
        <b:if cond='data:post.allowComments'>
          <b:if cond='data:post.feedLinks'>
            <b:include data='post.feedLinks' name='feedLinksBody'/>
          </b:if>
        </b:if>
      </b:loop>
    </div>
  </b:if>
</b:includable>
                        <b:includable id='feedLinksBody' var='links'>
  <div class='feed-links'>
  <data:feedLinksMsg/>
  <b:loop values='data:links' var='f'>
     <a class='feed-link' expr:href='data:f.url' expr:type='data:f.mimeType' target='_blank'><data:f.name/> (<data:f.feedType/>)</a>
  </b:loop>
  </div>
</b:includable>
                        <b:includable id='iframe_comments' var='post'>

  <b:if cond='data:post.allowIframeComments'>
    <script expr:src='data:post.iframeCommentSrc' type='text/javascript'/>
    <div class='cmt_iframe_holder' expr:data-href='data:post.canonicalUrl' expr:data-viewtype='data:post.viewType'/>

    <b:if cond='data:post.embedCommentForm == &quot;false&quot;'>
      <a expr:href='data:post.addCommentUrl' expr:onclick='data:post.addCommentOnclick'><data:postCommentMsg/></a>
    </b:if>
  </b:if>
</b:includable>
                        <b:includable id='mobile-index-post' var='post'>
  <div class='mobile-date-outer date-outer'>
    <b:if cond='data:post.dateHeader'>
      <div class='date-header'>
        <span><data:post.dateHeader/></span>
      </div>
    </b:if>

    <div class='mobile-post-outer'>
      <a expr:href='data:post.url'>
        <h3 class='mobile-index-title entry-title' itemprop='name'>
          <data:post.title/>
        </h3>

        <div class='mobile-index-arrow'>&amp;rsaquo;</div>

        <div class='mobile-index-contents'>
          <b:if cond='data:post.thumbnailUrl'>
            <div class='mobile-index-thumbnail'>
              <div class='Image'>
                <img expr:src='data:post.thumbnailUrl'/>
              </div>
            </div>
          </b:if>

          <div class='post-body'>
            <b:if cond='data:post.snippet'><data:post.snippet/></b:if>
          </div>
        </div>

        <div style='clear: both;'/>
      </a>

      <div class='mobile-index-comment'>
        <b:if cond='data:blog.pageType != &quot;static_page&quot;'>
          <b:if cond='data:post.allowComments'>
            <b:if cond='data:post.numComments != 0'>
              <a class='comment-link' expr:href='data:post.addCommentUrl' expr:onclick='data:post.addCommentOnclick'><b:if cond='data:post.numComments == 1'>1 <data:top.commentLabel/><b:else/><data:post.numComments/> <data:top.commentLabelPlural/></b:if></a>
            </b:if>
          </b:if>
        </b:if>
      </div>
    </div>
  </div>
</b:includable>
                        <b:includable id='mobile-main' var='top'>
    <!-- posts -->
    <div class='blog-posts hfeed'>

      <b:include data='top' name='status-message'/>

      <b:if cond='data:blog.pageType == &quot;index&quot;'>
        <b:loop values='data:posts' var='post'>
          <b:include data='post' name='mobile-index-post'/>
        </b:loop>
      <b:else/>
        <b:loop values='data:posts' var='post'>
          <b:include data='post' name='mobile-post'/>
        </b:loop>
      </b:if>
    </div>

   <b:include name='mobile-nextprev'/>
</b:includable>
                        <b:includable id='mobile-nextprev'>
  <div class='blog-pager' id='blog-pager'>
    <b:if cond='data:newerPageUrl'>
      <div class='mobile-link-button' id='blog-pager-newer-link'>
      <a class='blog-pager-newer-link' expr:href='data:newerPageUrl' expr:id='data:widget.instanceId + &quot;_blog-pager-newer-link&quot;' expr:title='data:newerPageTitle'>&amp;lsaquo;</a>
      </div>
    </b:if>

    <b:if cond='data:olderPageUrl'>
      <div class='mobile-link-button' id='blog-pager-older-link'>
      <a class='blog-pager-older-link' expr:href='data:olderPageUrl' expr:id='data:widget.instanceId + &quot;_blog-pager-older-link&quot;' expr:title='data:olderPageTitle'>&amp;rsaquo;</a>
      </div>
    </b:if>

    <div class='mobile-link-button' id='blog-pager-home-link'>
    <a class='home-link' expr:href='data:blog.homepageUrl'><data:homeMsg/></a>
    </div>

    <div class='mobile-desktop-link'>
      <a class='home-link' expr:href='data:desktopLinkUrl'><data:desktopLinkMsg/></a>
    </div>

  </div>
  <div class='clear'/>
</b:includable>
                        <b:includable id='mobile-post' var='post'>
  <div class='date-outer'>
    <b:if cond='data:post.dateHeader'>
      <h2 class='date-header'><span><data:post.dateHeader/></span></h2>
    </b:if>
    <div class='date-posts'>
      <div class='post-outer'>

        <div class='post hentry uncustomized-post-template'>
          <a expr:name='data:post.id'/>
          <b:if cond='data:post.title'>
            <h1 class='post-title entry-title'>
              <b:if cond='data:post.link'>
                <a expr:href='data:post.link'><data:post.title/></a>
              <b:else/>
                <b:if cond='data:post.url'>
                  <b:if cond='data:blog.url != data:post.url'>
                    <a expr:href='data:post.url'><data:post.title/></a>
                  <b:else/>
                    <data:post.title/>
                  </b:if>
                <b:else/>
                  <data:post.title/>
                </b:if>
              </b:if>
            </h1>
          </b:if>

          <div class='post-header'>
            <div class='post-header-line-1'/>
          </div>

          <div class='post-body entry-content' expr:id='&quot;post-body-&quot; + data:post.id'>
            <data:post.body/>
            <div style='clear: both;'/> <!-- clear for photos floats -->
          </div>

          <div class='post-footer'>
            <div class='post-footer-line post-footer-line-1'>
              <span class='post-author vcard'>
                <b:if cond='data:top.showAuthor'>
                  <b:if cond='data:post.authorProfileUrl'>
                    <span class='fn'>
                      <a expr:href='data:post.authorProfileUrl' rel='author' title='author profile'>
                        <data:post.author/>
                      </a>
                    </span>
                  <b:else/>
                    <span class='fn'><data:post.author/></span>
                  </b:if>
                </b:if>
              </span>

              <span class='post-timestamp'>
                <b:if cond='data:top.showTimestamp'>
                  <data:top.timestampLabel/>
                  <b:if cond='data:post.url'>
                    <a class='timestamp-link' expr:href='data:post.url' rel='bookmark' title='permanent link'><abbr class='published' expr:title='data:post.timestampISO8601'><data:post.timestamp/></abbr></a>
                  </b:if>
                </b:if>
              </span>

              <span class='post-comment-link'>
                <b:if cond='data:blog.pageType != &quot;item&quot;'>
                  <b:if cond='data:blog.pageType != &quot;static_page&quot;'>
                    <b:if cond='data:post.allowComments'>
                      <a class='comment-link' expr:href='data:post.addCommentUrl' expr:onclick='data:post.addCommentOnclick'><b:if cond='data:post.numComments == 1'>1 <data:top.commentLabel/><b:else/><data:post.numComments/> <data:top.commentLabelPlural/></b:if></a>
                    </b:if>
                  </b:if>
                </b:if>
              </span>
            </div>

            <div class='post-footer-line post-footer-line-2'>
              <b:if cond='data:top.showMobileShare'>
                <div class='mobile-link-button goog-inline-block' id='mobile-share-button'>
                  <a href='javascript:void(0);'><data:shareMsg/></a>
                </div>
              </b:if>
              <b:if cond='data:top.showDummy'>
                <div class='goog-inline-block dummy-container'><data:post.dummyTag/></div>
              </b:if>
            </div>

          </div>
        </div>
        <b:if cond='data:blog.pageType == &quot;static_page&quot;'>
          <b:include data='post' name='comment_picker'/>
        </b:if>
        <b:if cond='data:blog.pageType == &quot;item&quot;'>
          <b:include data='post' name='comment_picker'/>
        </b:if>
      </div>
    </div>
  </div>
</b:includable>
                        <b:includable id='nextprev'>
  <div class='blog-pager' id='blog-pager'>
    <b:if cond='data:newerPageUrl'>
      <span id='blog-pager-newer-link'>
      <a class='blog-pager-newer-link' expr:href='data:newerPageUrl' expr:id='data:widget.instanceId + &quot;_blog-pager-newer-link&quot;' expr:title='data:newerPageTitle'><data:newerPageTitle/></a>
      </span>
    </b:if>

    <b:if cond='data:olderPageUrl'>
      <span id='blog-pager-older-link'>
<a class='blog-pager-older-link' expr:href='data:olderPageUrl' expr:id='data:widget.instanceId + &quot;_blog-pager-older-link&quot;' expr:title='data:olderPageTitle'><data:olderPageTitle/></a>
      </span>
    </b:if>

    <a class='home-link' expr:href='data:blog.homepageUrl'><data:homeMsg/></a>

    <b:if cond='data:mobileLinkUrl'>
      <div class='blog-mobile-link'>
        <a expr:href='data:mobileLinkUrl'><data:mobileLinkMsg/></a>
      </div>
    </b:if>

  </div>
  <div class='clear'/>
</b:includable>
                        <b:includable id='post' var='post'>
  <div class='post hentry' itemprop='blogPost' itemscope='itemscope' itemtype='http://schema.org/BlogPosting'>


<b:if cond='data:blog.pageType == &quot;item&quot;'>

<b:if cond='data:post.title'>
<h1 class='article_post_title entry-title' itemprop='name'>
<b:if cond='data:post.link'>       
<a expr:href='data:post.link'><data:post.title/></a>    
<b:else/>
<b:if cond='data:post.url'>
<b:if cond='data:blog.url != data:post.url'>
<a expr:href='data:post.url'><data:post.title/></a>
<b:else/>
<data:post.title/>
</b:if>
<b:else/>
<data:post.title/>
</b:if>
</b:if>
</h1>
</b:if>
<b:else/>
<b:if cond='data:blog.pageType == &quot;static_page&quot;'>
<b:if cond='data:post.title'>
<h2 class='post-title entry-title' itemprop='name'>
 <b:if cond='data:post.link'>
<a expr:href='data:post.link'><data:post.title/></a>  
<b:else/>
<b:if cond='data:post.url'>
<b:if cond='data:blog.url != data:post.url'>
<a expr:href='data:post.url'><data:post.title/></a>
<b:else/>
<data:post.title/>
</b:if>
<b:else/>
<data:post.title/>
</b:if>
</b:if>
</h2>
</b:if>
</b:if>
</b:if>

  <b:if cond='data:blog.pageType == &quot;item&quot;'>

<div class='meta post_meta'>
<span class='date'><data:post.timestamp/></span>

<span class='author'>by <a expr:href='data:post.authorProfileUrl'><data:post.author/></a></span>

<span class='comment'>
<a expr:href='data:post.addCommentUrl' expr:onclick='data:post.addCommentOnclick'><b:if cond='data:post.numComments == 0'>No Comment</b:if><b:if cond='data:post.numComments == 1'>1 comment</b:if><b:if cond='data:post.numComments &gt;= 2'><data:post.numComments/> comments</b:if></a>
      </span>
      
<!-- @social_share_icons -->
<div class='share-wrapper' style='margin:15px 0 0 0 !important'>	
<ul class='entry-share-list clearfix'>
<li class='facebook_share' style='margin:0 !important'>
<a expr:href='&quot;http://www.facebook.com/sharer.php?u=&quot; + data:post.url + &quot;&amp;title=&quot;+ data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-facebook'/> <span class='share-text'>Facebook</span></a>
</li>
<li class='twitter_share' style='margin:0 !important'>
<a expr:href='&quot;http://twitter.com/share?url=&quot; + data:post.url + &quot;&amp;title=&quot; + data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-twitter'/> <span class='share-text'>Twitter</span></a>
</li>
<li class='google_share' style='margin:0 !important'>
<a expr:href='&quot;https://plus.google.com/share?url=&quot; + data:post.url + &quot;&amp;title=&quot; + data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-google-plus'/><span class='share-text'>Google+</span></a>
</li>
<li class='linkedin_share' style='margin:0 !important'>
<a expr:href='&quot;http://www.linkedin.com/shareArticle?mini=true&amp;url=&quot; + data:post.url + &quot;&amp;title=&quot;+ data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-linkedin'/><span class='share-text'>Linkedin</span></a>
</li>
<li class='pinterest_share' style='margin:0 !important'><a expr:href='&quot;http://pinterest.com/pin/create/button/?url=&quot; + data:post.url + &quot;&amp;media=&quot; + data:post.thumbnailUrl + &quot;&amp;description=&quot; + data:post.snippet' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow'><i class='fa fa-pinterest'/><span class='share-text'>Pin It</span></a></li>


</ul><!-- .entry-share-icons -->
</div>
<b:section class='content2' id='content2' showaddelement='no'>
<b:widget id='HTML5' locked='false' title='adstop' type='HTML'>
                        <b:includable id='main'>
  <!-- only display title if it's non-empty -->
  <b:if cond='data:title != &quot;&quot;'>
    <h2 class='title'><data:title/></h2>
  </b:if>
  <div class='widget-content'>
    <data:content/>
  </div>

  <b:include name='quickedit'/>
</b:includable>
                      </b:widget>
</b:section>
    </div>

    </b:if>

    <div class='post-body entry-content' expr:id='&quot;post-body-&quot; + data:post.id'>
      <b:if cond='data:blog.pageType == &quot;item&quot;'>
<data:post.body/>
<b:else/>
<b:if cond='data:blog.pageType == &quot;static_page&quot;'>
<data:post.body/>
<b:else/>
<div expr:id='&quot;p&quot; + data:post.id'>
<data:post.body/>
</div>


<div class='article_footer clearfix'>



<div class='meta-item share'>

<ul class='social-list'>
<li><a class='art_twitter' data-title='Twitter' expr:href='&quot;http://twitter.com/share?url=&quot; + data:post.url + &quot;&amp;title=&quot; + data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-twitter ct-transition-up'/></a></li>

<li><a class='art_facebook' data-title='Facebook' expr:href='&quot;http://www.facebook.com/sharer.php?u=&quot; + data:post.url + &quot;&amp;title=&quot;+ data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-facebook ct-transition-up'/></a></li>

<li><a class='art_google' data-title='Google+' expr:href='&quot;https://plus.google.com/share?url=&quot; + data:post.url + &quot;&amp;title=&quot; + data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-google-plus ct-transition-up-up'/></a></li>

</ul>

<a class='button' href='#social_share'><i class='fa fa-mail-forward'/></a>
</div>

</div>

<script type='text/javascript'>
var x= &quot;<data:post.title/>&quot;;rm(&quot;p<data:post.id/>&quot;,&quot;<data:post.url/>&quot;,&quot;<data:post.timestamp/>&quot;,&quot;<data:post.numComments/>&quot;,&quot;<b:loop values='data:post.labels' var='label'><a expr:href='data:label.url + &quot;?max-results=6&quot;' rel='tag'><data:label.name/></a></b:loop>&quot;,&quot;<a expr:href='data:post.authorProfileUrl'><data:post.author/></a>&quot;);
</script>



</b:if>
</b:if>
      <div style='clear: both;'/> <!-- clear for photos floats -->
    </div>

<b:if cond='data:blog.pageType == &quot;item&quot;'>
<div class='post-footer'>

<div class='meta post_tags'>
<i class='fa fa-bookmark'/> Tagged in : <b:loop values='data:post.labels' var='label'><a expr:href='data:label.url + &quot;?max-results=6&quot;' rel='tag'><data:label.name/></a>, </b:loop>
</div>

<!-- @social_share_icons -->
<div class='share-wrapper'>	
<div class='title'>&#8212; Jika dirasa bermanfaat, jangan lupa di Share ya. &#8212;</div>
<ul class='entry-share-list clearfix'>
<li class='facebook_share'>
<a expr:href='&quot;http://www.facebook.com/sharer.php?u=&quot; + data:post.url + &quot;&amp;title=&quot;+ data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-facebook'/> <span class='share-text'>Facebook</span></a>
</li>
<li class='twitter_share'>
<a expr:href='&quot;http://twitter.com/share?url=&quot; + data:post.url + &quot;&amp;title=&quot; + data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-twitter'/> <span class='share-text'>Twitter</span></a>
</li>
<li class='google_share'>
<a expr:href='&quot;https://plus.google.com/share?url=&quot; + data:post.url + &quot;&amp;title=&quot; + data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-google-plus'/><span class='share-text'>Google+</span></a>
</li>
<li class='linkedin_share'>
<a expr:href='&quot;http://www.linkedin.com/shareArticle?mini=true&amp;url=&quot; + data:post.url + &quot;&amp;title=&quot;+ data:post.title' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow' target='_blank'><i class='fa fa-linkedin'/><span class='share-text'>Linkedin</span></a>
</li>
<li class='pinterest_share'><a expr:href='&quot;http://pinterest.com/pin/create/button/?url=&quot; + data:post.url + &quot;&amp;media=&quot; + data:post.thumbnailUrl + &quot;&amp;description=&quot; + data:post.snippet' onclick='window.open(this.href, &apos;windowName&apos;, &apos;width=550, height=600, left=24, top=24, scrollbars, resizable&apos;); return false;' rel='nofollow'><i class='fa fa-pinterest'/><span class='share-text'>Pin It</span></a></li>


</ul><!-- .entry-share-icons -->
</div>



<!-- blog_post_author_profile -->    
<div class='authorboxwrap'>
<div class='authorboxfull'>
<div class='avatar-container'>
<a href=''>
<img class='author_avatar img-circle' expr:alt='data:post.author' expr:src='data:post.authorPhoto.url' height='96' width='96'/>
</a>
</div>
<div class='author_description_container'>
<h4><a href='#' rel='author'><data:post.author/></a></h4>
<p>
<data:post.authorAboutMe/>       
</p>
<div class='authorsocial'>
<a class='img-circle' href='http://facebook.com/username' target='_blank'><i class='fa fa-facebook'/></a>
<a class='' href='http://twitter.com/username' target='_blank'><i class='fa fa-twitter'/></a>
<a class='' href='http://dribbble.com/username' target='_blank'><i class='fa fa-dribbble'/></a>
<a class='' href='http://instagram.com/username' target='_blank'><i class='fa fa-instagram'/></a>
<div class='clr'/>
</div>
</div>
</div>
</div>






<!-- @related-posts -->
<div id='related-posts'>
<script type='text/javascript'>//<![CDATA[
var ry="<h5>Related Posts</h5>";rn="<h5>No related post available</h5>";rcomment="comments";rdisable="disable comments";commentYN="no";var dw="";titles=new Array;titlesNum=0;urls=new Array;timeR=new Array;thumb=new Array;commentsNum=new Array;comments=new Array;
function related_results_labels(c){for(var b=0;b<c.feed.entry.length;b++){var d=c.feed.entry[b];titles[titlesNum]=d.title.$t;for(var a=0;a<d.link.length;a++){if("thr$total"in d)commentsNum[titlesNum]=d.thr$total.$t+" "+rcomment;else commentsNum[titlesNum]=rdisable;if(d.link[a].rel=="alternate"){urls[titlesNum]=d.link[a].href;timeR[titlesNum]=d.published.$t;if("media$thumbnail"in d)thumb[titlesNum]=d.media$thumbnail.url;else thumb[titlesNum]="http://lh3.ggpht.com/--Z8SVBQZ4X8/TdDxPVMl_sI/AAAAAAAAAAA/jhAgjCpZtRQ/no-image.png";
titlesNum++;break}}}}function removeRelatedDuplicates(){var b=new Array(0);c=new Array(0);e=new Array(0);f=new Array(0);g=new Array(0);for(var a=0;a<urls.length;a++)if(!contains(b,urls[a])){b.length+=1;b[b.length-1]=urls[a];c.length+=1;c[c.length-1]=titles[a];e.length+=1;e[e.length-1]=timeR[a];f.length+=1;f[f.length-1]=thumb[a];g.length+=1;g[g.length-1]=commentsNum[a]}urls=b;titles=c;timeR=e;thumb=f;commentsNum=g}
function contains(b,d){for(var c=0;c<b.length;c++)if(b[c]==d)return true;return false}
function printRelatedLabels(a){var y=a.indexOf("?m=0");if(y!=-1)a=a.replace(/\?m=0/g,"");for(var b=0;b<urls.length;b++)if(urls[b]==a){urls.splice(b,1);titles.splice(b,1);timeR.splice(b,1);thumb.splice(b,1);commentsNum.splice(b,1)}var c=Math.floor((titles.length-1)*Math.random());var b=0;if(titles.length==0)dw+=rn;else{dw+=ry;dw+="<ul>";while(b<titles.length&&b<20&&b<maxresults){if(y!=-1)urls[c]=urls[c]+"?m=0";if(commentYN=="yes")comments[c]=" - "+commentsNum[c];else comments[c]="";dw+='<li><a href="'+
urls[c]+'" title="'+titles[c]+'" rel="nofollow" class="related-thumbs"><img alt="'+titles[c]+'" src="'+thumb[c].replace(/\/s72\-c/,"/s"+size+"-c")+'"/></a><div class="clr"></div><a class="related-title" href="'+urls[c]+'">'+titles[c]+"</a></li></div>";if(c<titles.length-1)c++;else c=0;b++}dw+="</ul>"}urls.splice(0,urls.length);titles.splice(0,titles.length);document.getElementById("related-posts").innerHTML=dw};

//]]></script>
<b:loop values='data:post.labels' var='label'>
<script expr:src='&quot;/feeds/posts/default/-/&quot; + data:label.name + &quot;?alt=json-in-script&amp;callback=related_results_labels&quot;' type='text/javascript'/>
</b:loop>
<script type='text/javascript'>
var maxresults=3;
var size = 250;
removeRelatedDuplicates();
printRelatedLabels(&#39;<data:post.url/>&#39;);</script>
</div>

<div class='post-footer-line post-footer-line-2'>

 </div>

      <div class='post-footer-line post-footer-line-3'><span class='post-location'>
        <b:if cond='data:top.showLocation'>
          <b:if cond='data:post.location'>
            <data:postLocationLabel/>
            <a expr:href='data:post.location.mapsUrl' target='_blank'><data:post.location.name/></a>
          </b:if>
        </b:if>
      </span> </div>
    </div>
</b:if>

  </div>
</b:includable>
                        <b:includable id='postQuickEdit' var='post'>
  <b:if cond='data:post.editUrl'>
    <span expr:class='&quot;item-control &quot; + data:post.adminClass'>
      <a expr:href='data:post.editUrl' expr:title='data:top.editPostMsg'>
        <img alt='' class='icon-action' height='18' src='http://img2.blogblog.com/img/icon18_edit_allbkg.gif' width='18'/>
      </a>
    </span>
  </b:if>
</b:includable>
                        <b:includable id='shareButtons' var='post'>
  <b:if cond='data:top.showEmailButton'><a class='goog-inline-block share-button sb-email' expr:href='data:post.sharePostUrl + &quot;&amp;target=email&quot;' expr:title='data:top.emailThisMsg' target='_blank'><span class='share-button-link-text'><data:top.emailThisMsg/></span></a></b:if><b:if cond='data:top.showBlogThisButton'><a class='goog-inline-block share-button sb-blog' expr:href='data:post.sharePostUrl + &quot;&amp;target=blog&quot;' expr:onclick='&quot;window.open(this.href, \&quot;_blank\&quot;, \&quot;height=270,width=475\&quot;); return false;&quot;' expr:title='data:top.blogThisMsg' target='_blank'><span class='share-button-link-text'><data:top.blogThisMsg/></span></a></b:if><b:if cond='data:top.showTwitterButton'><a class='goog-inline-block share-button sb-twitter' expr:href='data:post.sharePostUrl + &quot;&amp;target=twitter&quot;' expr:title='data:top.shareToTwitterMsg' target='_blank'><span class='share-button-link-text'><data:top.shareToTwitterMsg/></span></a></b:if><b:if cond='data:top.showFacebookButton'><a class='goog-inline-block share-button sb-facebook' expr:href='data:post.sharePostUrl + &quot;&amp;target=facebook&quot;' expr:onclick='&quot;window.open(this.href, \&quot;_blank\&quot;, \&quot;height=430,width=640\&quot;); return false;&quot;' expr:title='data:top.shareToFacebookMsg' target='_blank'><span class='share-button-link-text'><data:top.shareToFacebookMsg/></span></a></b:if><b:if cond='data:top.showOrkutButton'><a class='goog-inline-block share-button sb-orkut' expr:href='data:post.sharePostUrl + &quot;&amp;target=orkut&quot;' expr:title='data:top.shareToOrkutMsg' target='_blank'><span class='share-button-link-text'><data:top.shareToOrkutMsg/></span></a></b:if><b:if cond='data:top.showDummy'><div class='goog-inline-block dummy-container'><data:post.dummyTag/></div></b:if>
</b:includable>
                        <b:includable id='status-message'>
  <b:if cond='data:navMessage'>
  <div class='status-msg-wrap'>
    <div class='status-msg-body'>
      <data:navMessage/>
    </div>
    <div class='status-msg-border'>
      <div class='status-msg-bg'>
        <div class='status-msg-hidden'><data:navMessage/></div>
      </div>
    </div>
  </div>
  <div style='clear: both;'/>
  </b:if>
</b:includable>
                        <b:includable id='threaded-comment-form' var='post'>
  <div class='comment-form'>
    <a name='comment-form'/>
    <b:if cond='data:mobile'>
      <p><data:blogCommentMessage/></p>
      <data:blogTeamBlogMessage/>
      <a expr:href='data:post.commentFormIframeSrc' id='comment-editor-src'/>
      <iframe allowtransparency='true' class='blogger-iframe-colorize blogger-comment-from-post' frameborder='0' height='410' id='comment-editor' name='comment-editor' src='' style='display: none' width='100%'/>
    <b:else/>
      <p><data:blogCommentMessage/></p>
      <data:blogTeamBlogMessage/>
      <a expr:href='data:post.commentFormIframeSrc' id='comment-editor-src'/>
      <iframe allowtransparency='true' class='blogger-iframe-colorize blogger-comment-from-post' frameborder='0' height='410' id='comment-editor' name='comment-editor' src='' width='100%'/>
    </b:if>
    <data:post.friendConnectJs/>
    <data:post.cmtfpIframe/>
    <script type='text/javascript'>
      BLOG_CMT_createIframe(&#39;<data:post.appRpcRelayPath/>&#39;, &#39;<data:post.communityId/>&#39;);
    </script>
  </div>
</b:includable>
                        <b:includable id='threaded_comment_css'>
  <style>

  </style>
</b:includable>
                        <b:includable id='threaded_comment_js' var='post'>
  <script async='async' expr:src='data:post.commentSrc' type='text/javascript'/>

  <script type='text/javascript'>
    (function() {
      var items = <data:post.commentJso/>;
      var msgs = <data:post.commentMsgs/>;
      var config = <data:post.commentConfig/>;

// <![CDATA[
      var cursor = null;
      if (items && items.length > 0) {
        cursor = parseInt(items[items.length - 1].timestamp) + 1;
      }

      var bodyFromEntry = function(entry) {
        if (entry.gd$extendedProperty) {
          for (var k in entry.gd$extendedProperty) {
            if (entry.gd$extendedProperty[k].name == 'blogger.contentRemoved') {
              return '<span class="deleted-comment">' + entry.content.$t + '</span>';
            }
          }
        }
        return entry.content.$t;
      }

      var parse = function(data) {
        cursor = null;
        var comments = [];
        if (data && data.feed && data.feed.entry) {
          for (var i = 0, entry; entry = data.feed.entry[i]; i++) {
            var comment = {};
            // comment ID, parsed out of the original id format
            var id = /blog-(\d+).post-(\d+)/.exec(entry.id.$t);
            comment.id = id ? id[2] : null;
            comment.body = bodyFromEntry(entry);
            comment.timestamp = Date.parse(entry.published.$t) + '';
            if (entry.author && entry.author.constructor === Array) {
              var auth = entry.author[0];
              if (auth) {
                comment.author = {
                  name: (auth.name ? auth.name.$t : undefined),
                  profileUrl: (auth.uri ? auth.uri.$t : undefined),
                  avatarUrl: (auth.gd$image ? auth.gd$image.src : undefined)
                };
              }
            }
            if (entry.link) {
              if (entry.link[2]) {
                comment.link = comment.permalink = entry.link[2].href;
              }
              if (entry.link[3]) {
                var pid = /.*comments\/default\/(\d+)\?.*/.exec(entry.link[3].href);
                if (pid && pid[1]) {
                  comment.parentId = pid[1];
                }
              }
            }
            comment.deleteclass = 'item-control blog-admin';
            if (entry.gd$extendedProperty) {
              for (var k in entry.gd$extendedProperty) {
                if (entry.gd$extendedProperty[k].name == 'blogger.itemClass') {
                  comment.deleteclass += ' ' + entry.gd$extendedProperty[k].value;
                } else if (entry.gd$extendedProperty[k].name == 'blogger.displayTime') {
                  comment.displayTime = entry.gd$extendedProperty[k].value;
                }
              }
            }
            comments.push(comment);
          }
        }
        return comments;
      };

      var paginator = function(callback) {
        if (hasMore()) {
          var url = config.feed + '?alt=json&v=2&orderby=published&reverse=false&max-results=50';
          if (cursor) {
            url += '&published-min=' + new Date(cursor).toISOString();
          }
          window.bloggercomments = function(data) {
            var parsed = parse(data);
            cursor = parsed.length < 50 ? null
                : parseInt(parsed[parsed.length - 1].timestamp) + 1
            callback(parsed);
            window.bloggercomments = null;
          }
          url += '&callback=bloggercomments';
          var script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = url;
          document.getElementsByTagName('head')[0].appendChild(script);
        }
      };
      var hasMore = function() {
        return !!cursor;
      };
      var getMeta = function(key, comment) {
        if ('iswriter' == key) {
          var matches = !!comment.author
              && comment.author.name == config.authorName
              && comment.author.profileUrl == config.authorUrl;
          return matches ? 'true' : '';
        } else if ('deletelink' == key) {
          return config.baseUri + '/delete-comment.g?blogID='
               + config.blogId + '&postID=' + comment.id;
        } else if ('deleteclass' == key) {
          return comment.deleteclass;
        }
        return '';
      };

      var replybox = null;
      var replyUrlParts = null;
      var replyParent = undefined;

      var onReply = function(commentId, domId) {
        if (replybox == null) {
          // lazily cache replybox, and adjust to suit this style:
          replybox = document.getElementById('comment-editor');
          if (replybox != null) {
            replybox.height = '250px';
            replybox.style.display = 'block';
            replyUrlParts = replybox.src.split('#');
          }
        }
        if (replybox && (commentId !== replyParent)) {
          document.getElementById(domId).insertBefore(replybox, null);
          replybox.src = replyUrlParts[0]
              + (commentId ? '&parentID=' + commentId : '')
              + '#' + replyUrlParts[1];
          replyParent = commentId;
        }
      };

      var hash = (window.location.hash || '#').substring(1);
      var startThread, targetComment;
      if (/^comment-form_/.test(hash)) {
        startThread = hash.substring('comment-form_'.length);
      } else if (/^c[0-9]+$/.test(hash)) {
        targetComment = hash.substring(1);
      }

      // Configure commenting API:
      var configJso = {
        'maxDepth': config.maxThreadDepth
      };
      var provider = {
        'id': config.postId,
        'data': items,
        'loadNext': paginator,
        'hasMore': hasMore,
        'getMeta': getMeta,
        'onReply': onReply,
        'rendered': true,
        'initComment': targetComment,
        'initReplyThread': startThread,
        'config': configJso,
        'messages': msgs
      };

      var render = function() {
        if (window.goog && window.goog.comments) {
          var holder = document.getElementById('comment-holder');
          window.goog.comments.render(holder, provider);
        }
      };

      // render now, or queue to render when library loads:
      if (window.goog && window.goog.comments) {
        render();
      } else {
        window.goog = window.goog || {};
        window.goog.comments = window.goog.comments || {};
        window.goog.comments.loadQueue = window.goog.comments.loadQueue || [];
        window.goog.comments.loadQueue.push(render);
      }
    })();
// ]]>
  </script>
</b:includable>
                        <b:includable id='threaded_comments' var='post'>
  <div class='comments' id='comments'>
    <a name='comments'/>
    <h4><data:post.commentLabelFull/>:</h4>

    <div class='comments-content'>
      <b:if cond='data:post.embedCommentForm'>
        <b:include data='post' name='threaded_comment_js'/>
      </b:if>
      <div id='comment-holder'>
         <data:post.commentHtml/>
      </div>
    </div>

    <p class='comment-footer'>
      <b:if cond='data:post.allowNewComments'>
        <b:include data='post' name='threaded-comment-form'/>
      <b:else/>
        <data:post.noNewCommentsText/>
      </b:if>
    </p>

    <b:if cond='data:showCmtPopup'>
      <div id='comment-popup'>
        <iframe allowtransparency='true' frameborder='0' id='comment-actions' name='comment-actions' scrolling='no'>
        </iframe>
      </div>
    </b:if>

    <div id='backlinks-container'>
    <div expr:id='data:widget.instanceId + &quot;_backlinks-container&quot;'>
       <b:if cond='data:post.showBacklinks'>
         <b:include data='post' name='backlinks'/>
       </b:if>
    </div>
    </div>
  </div>
</b:includable>
                      </b:widget>
                    </b:section>
             </div><!-- /main-wrapper -->

                   <div class='sidebar-wrapper'>
                     <b:section class='sidebar' id='sidebar' preferred='yes' showaddelement='yes'>
                       <b:widget id='HTML2' locked='false' title='Update on Facebook' type='HTML'>
                         <b:includable id='main'>
  <!-- only display title if it's non-empty -->
  <b:if cond='data:title != &quot;&quot;'>
    <h2 class='title'><data:title/></h2>
  </b:if>
  <div class='widget-content'>
    <data:content/>
  </div>

  <b:include name='quickedit'/>
</b:includable>
                       </b:widget>
                       <b:widget id='HTML1' locked='false' title='Featured Posts' type='HTML'>
                         <b:includable id='main'>
<b:if cond='data:blog.pageType == &quot;index&quot;'>
  
<b:if cond='data:title'><h2><data:title/></h2></b:if>
                           <div class='widget-content'>
                             
<script>
document.write(&quot;&lt;script src=\&quot;/feeds/posts/default/-/&quot;+cat1+&quot;?max-results=&quot;+numposts1+&quot;&amp;orderby=published&amp;alt=json-in-script&amp;callback=recentposts1\&quot;&gt;&lt;\/script&gt;&quot;);
</script>
                           </div>
                           </b:if>
                         </b:includable>
                       </b:widget>
                       <b:widget id='HTML3' locked='false' title='' type='HTML'>
                         <b:includable id='main'>
  <!-- only display title if it's non-empty -->
  <b:if cond='data:title != &quot;&quot;'>
    <h2 class='title'><data:title/></h2>
  </b:if>
  <div class='widget-content'>
    <data:content/>
  </div>

  <b:include name='quickedit'/>
</b:includable>
                       </b:widget>
                       <b:widget id='PopularPosts100' locked='false' title='Popular Posts' type='PopularPosts'>
                         <b:includable id='main'>
  <b:if cond='data:title'><h2><data:title/></h2></b:if>
  <div class='widget-content popular-posts'>
    <ul>
      <b:loop values='data:posts' var='post'>
      <li>
        <b:if cond='data:showThumbnails == &quot;false&quot;'>
          <b:if cond='data:showSnippets == &quot;false&quot;'>
            <!-- (1) No snippet/thumbnail -->
            <a expr:href='data:post.href'><data:post.title/></a>
          <b:else/>
            <!-- (2) Show only snippets -->
            <div class='item-title'><a expr:href='data:post.href'><data:post.title/></a></div>
            <div class='item-snippet'><data:post.snippet/></div>
          </b:if>
        <b:else/>
          <b:if cond='data:showSnippets == &quot;false&quot;'>
            <!-- (3) Show only thumbnails -->
            <div class='item-thumbnail-only'>
              <b:if cond='data:post.thumbnail'>
                <div class='item-thumbnail'>
                  <a expr:href='data:post.href' target='_blank'>
                    <img alt='' border='0' expr:height='data:thumbnailSize' expr:src='data:post.thumbnail' expr:width='data:thumbnailSize'/>
                  </a>
                </div>
              </b:if>
              <div class='item-title'><a expr:href='data:post.href'><data:post.title/></a></div>
            </div>
            <div style='clear: both;'/>
          <b:else/>
            <!-- (4) Show snippets and thumbnails -->
            <div class='item-content'>
              <b:if cond='data:post.thumbnail'>
                <div class='item-thumbnail'>
                  <a expr:href='data:post.href' target='_blank'>
                    <img alt='' border='0' expr:height='data:thumbnailSize' expr:src='data:post.thumbnail' expr:width='data:thumbnailSize'/>
                  </a>
                </div>
              </b:if>
              <div class='item-title'><a expr:href='data:post.href'><data:post.title/></a></div>
              <div class='item-snippet'><data:post.snippet/></div>
            </div>
            <div style='clear: both;'/>
          </b:if>
        </b:if>
      </li>
      </b:loop>
    </ul>
  </div>
</b:includable>
                       </b:widget>
                       <b:widget id='Label1' locked='false' title='Labels' type='Label'>
                         <b:includable id='main'>
  <b:if cond='data:title'>
    <h2><data:title/></h2>
  </b:if>
  <div expr:class='&quot;widget-content &quot; + data:display + &quot;-label-widget-content&quot;'>
    <b:if cond='data:display == &quot;list&quot;'>
      <ul>
      <b:loop values='data:labels' var='label'>
        <li>
          <b:if cond='data:blog.url == data:label.url'>
            <span expr:dir='data:blog.languageDirection'><data:label.name/></span>
          <b:else/>
            <a expr:dir='data:blog.languageDirection' expr:href='data:label.url'><data:label.name/></a>
          </b:if>
          <b:if cond='data:showFreqNumbers'>
            <span dir='ltr'>(<data:label.count/>)</span>
          </b:if>
        </li>
      </b:loop>
      </ul>
    <b:else/>
      <b:loop values='data:labels' var='label'>
        <span expr:class='&quot;label-size label-size-&quot; + data:label.cssSize'>
          <b:if cond='data:blog.url == data:label.url'>
            <span expr:dir='data:blog.languageDirection'><data:label.name/></span>
          <b:else/>
            <a expr:dir='data:blog.languageDirection' expr:href='data:label.url'><data:label.name/></a>
          </b:if>
          <b:if cond='data:showFreqNumbers'>
            <span class='label-count' dir='ltr'>(<data:label.count/>)</span>
          </b:if>
        </span>
      </b:loop>
    </b:if>
  </div>
</b:includable>
                       </b:widget>
                       <b:widget id='BlogArchive1' locked='false' title='Arsip Artikel' type='BlogArchive'>
                         <b:includable id='main'>
  <b:if cond='data:title != &quot;&quot;'>
    <h2><data:title/></h2>
  </b:if>
  <div class='widget-content'>
  <div id='ArchiveList'>
  <div expr:id='data:widget.instanceId + &quot;_ArchiveList&quot;'>
    <b:include cond='data:style == &quot;HIERARCHY&quot;' data='data' name='interval'/>
    <b:include cond='data:style == &quot;FLAT&quot;' data='data' name='flat'/>
    <b:include cond='data:style == &quot;MENU&quot;' data='data' name='menu'/>
  </div>
  </div>
  <b:include name='quickedit'/>
  </div>
</b:includable>
                         <b:includable id='flat' var='data'>
  <ul class='flat'>
    <b:loop values='data:data' var='i'>
      <li class='archivedate'>
        <a expr:href='data:i.url'><data:i.name/></a> (<data:i.post-count/>)
      </li>
    </b:loop>
  </ul>
</b:includable>
                         <b:includable id='interval' var='intervalData'>
  <b:loop values='data:intervalData' var='interval'>
    <ul class='hierarchy'>
      <li expr:class='&quot;archivedate &quot; + data:interval.expclass'>
        <b:include cond='data:interval.toggleId' data='interval' name='toggle'/>
        <a class='post-count-link' expr:href='data:interval.url'>
          <data:interval.name/>
        </a>
        <span class='post-count' dir='ltr'>(<data:interval.post-count/>)</span>
        <b:include cond='data:interval.data' data='interval.data' name='interval'/>
        <b:include cond='data:interval.posts' data='interval.posts' name='posts'/>
      </li>
    </ul>
  </b:loop>
</b:includable>
                         <b:includable id='menu' var='data'>
  <select expr:id='data:widget.instanceId + &quot;_ArchiveMenu&quot;'>
    <option value=''><data:title/></option>
    <b:loop values='data:data' var='i'>
      <option expr:value='data:i.url'><data:i.name/> (<data:i.post-count/>)</option>
    </b:loop>
  </select>
</b:includable>
                         <b:includable id='posts' var='posts'>
  <ul class='posts'>
    <b:loop values='data:posts' var='post'>
      <li><a expr:href='data:post.url'><data:post.title/></a></li>
    </b:loop>
  </ul>
</b:includable>
                         <b:includable id='toggle' var='interval'>
  <a class='toggle' href='javascript:void(0)'>
    <span expr:class='&quot;zippy&quot; + (data:interval.expclass == &quot;expanded&quot; ? &quot; toggle-open&quot; : &quot;&quot;)'>
      <b:if cond='data:interval.expclass == &quot;expanded&quot;'>
        &#9660;&#160;
      <b:elseif cond='data:blog.languageDirection == &quot;rtl&quot;'/>
        &#9668;&#160;
      <b:else/>
        &#9658;&#160;
      </b:if>
    </span>
  </a>
</b:includable>
                       </b:widget>
                       <b:widget id='Navbar1' locked='false' title='Navbar' type='Navbar'>
                         <b:includable id='main'>&lt;script type=&quot;text/javascript&quot;&gt;
    function setAttributeOnload(object, attribute, val) {
      if(window.addEventListener) {
        window.addEventListener(&#39;load&#39;,
          function(){ object[attribute] = val; }, false);
      } else {
        window.attachEvent(&#39;onload&#39;, function(){ object[attribute] = val; });
      }
    }
  &lt;/script&gt;
&lt;div id=&quot;navbar-iframe-container&quot;&gt;&lt;/div&gt;
&lt;script type=&quot;text/javascript&quot; src=&quot;https://apis.google.com/js/plusone.js&quot;&gt;&lt;/script&gt;
&lt;script type=&quot;text/javascript&quot;&gt;
        gapi.load(&quot;gapi.iframes:gapi.iframes.style.bubble&quot;, function() {
          if (gapi.iframes &amp;&amp; gapi.iframes.getContext) {
            gapi.iframes.getContext().openChild({
                url: &#39;https://www.blogger.com/navbar.g?targetBlogID\0756397529022965473711\46blogName\75WAJIB+BACA+%7C+Kumpulan+Informasi+LUCU+...\46publishMode\75PUBLISH_MODE_HOSTED\46navbarType\75DISABLED\46layoutType\75LAYOUTS\46searchRoot\75http://www.wajibbaca.com/search\46blogLocale\75en\46v\0752\46homepageUrl\75http://www.wajibbaca.com/\46blogFollowUrl\75https://plus.google.com/113403288456532505851\46vt\75-1431124648522748634&#39;,
                where: document.getElementById(&quot;navbar-iframe-container&quot;),
                id: &quot;navbar-iframe&quot;
            });
          }
        });
      &lt;/script&gt;&lt;script type=&quot;text/javascript&quot;&gt;
(function() {
var script = document.createElement(&#39;script&#39;);
script.type = &#39;text/javascript&#39;;
script.src = &#39;//pagead2.googlesyndication.com/pagead/js/google_top_exp.js&#39;;
var head = document.getElementsByTagName(&#39;head&#39;)[0];
if (head) {
head.appendChild(script);
}})();
&lt;/script&gt;
</b:includable>
                       </b:widget>
                     </b:section>
                   </div><!-- /sidebar-wrapper -->
                <div class='clr'/>
            </div><!-- /outer-wrapper -->
     </div><!-- /ct-wrapper -->

<div class='footer_bottom'>

<!-- subscription_box here -->
<div class='subscribe-footer'>
        <h1>Subscribe Kami</h1>
        <p>Subscribe sekarang juga untuk mendapatkan update content unik lainnya melalui email update setiap harinya!</p>
        <form action='http://feedburner.google.com/fb/a/mailverify' id='subscribe' method='post' onsubmit='window.open(&apos;http://feedburner.google.com/fb/a/mailverify?uri=yourid&apos;, &apos;popupwindow&apos;, &apos;scrollbars=yes,width=550,height=520&apos;);return true' target='popupwindow'>
<input name='uri' type='hidden' value='yourid'/>
<input name='loc' type='hidden' value='en_US'/>
<input id='sub_box' name='email' onblur='if (this.value == &quot;&quot;) {this.value = &quot;Email address&quot;;}' onfocus='if (this.value == &quot;Email address&quot;) {this.value = &quot;&quot;}' type='text' value='Email address'/>
<input id='sub_button' title='' type='submit' value='Subscribe'/>
</form>
    </div>

<div class='ct-wrapper clearfix'>
<div class='footer-attribution'>
<p>Copyright 2015 <a expr:href='data:blog.homepageUrl'><data:blog.title/></a><a href='http://www.veethemes.com' id='attri_bution' style='display:none !important'>Veethemes.com</a></p>
</div>

<div class='scroll_header'>
<a href='#Header1' id='scroll_top'><i class='fa fa-chevron-up'/></a></div>

</div>

</div>

  
<script src='http://yourjavascript.com/714142215143/owl-carousel-min.js' type='text/javascript'/>
     
<script type='text/javascript'>//<![CDATA[

jQuery(document).ready(function(e) {

e(".owl_carouselle").owlCarousel({
                items: 4,
                nav: false,
                responsive: true,
                lazyLoad: true,
                autoPlay: 5e3,
                stopOnHover: true,
				pagination: false,	
				navigation: true,
  				navigationText : ["<i class='fa fa-angle-right'></i>","<i class='fa fa-angle-left'></i>"]
   
        });

$(function() {
  $('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});

})


$(".popular-posts .item-thumbnail img").attr("src", function (e, t) {
  return t.replace("s72-c", "s180-c")
});


//]]></script>

<script src='http://yourjavascript.com/031422241414/magnific-popup.js' type='text/javascript'/>
<script>//<![CDATA[
jQuery('a.open_s').magnificPopup({
		type: 'inline',

		fixedContentPos: false,
		fixedBgPos: true,

		overflowY: 'auto',

		closeBtnInside: true,
		preloader: false,
		
		midClick: true,
		removalDelay: 300,
		mainClass: 'my-mfp-zoom-in',
		focus: '#search',

		// When elemened is focused, some mobile browsers in some cases zoom in
		// It looks not nice, so we disable it:
		callbacks: {
			beforeOpen: function() {
				if(jQuery(window).width() < 700) {
					this.st.focus = false;
				} else {
					this.st.focus = '#search';
				}
			}
		}
});

	
//]]></script>


<b:include data='blog' name='google-analytics'/>
</body>
</html>