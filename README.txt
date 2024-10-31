=== Related Posts Neural Network ===
Contributors: neiltking
Tags: related, suggestions, neural network, artificial intelligence, machine learning
License: Apache-2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0
Requires at least: 4.6
Tested up to: 6.7
Stable tag: 0.0.4
Author: Neil T King
Author URI: https://www.neiltking.com

A custom Neural Network (Artificial Intelligence) which will learn from your visitors and recommend content on your site based on what they visit.

== Description ==

<a href="https://www.neiltking.com/neuralnet/">Related Posts Neural Network</a> learns what visitors access on your website and builds a locally stored Neural Network (Artificial Intelligence). This is then used to suggest other posts, pages or content on your site that visitors may like. You will have seen something similar on sites where they say "You may also like..." or "Other visitors were interested in...".

Some key features include:

*   As it works by examining the URL, it will work with all content types in all languages which users may visit, for example articles, products, pages, blog posts, courses, photo galleries etc.
*   No visitor statistics or data leaves your website. Nothing is processed externally. Everything is done locally using no third party services. The custom written A.I. is built entirely within the plugin.
*   The plugin is written to be efficient so there should not be any noticable affect on the speed of your website.
*   No identifiable or personal information is collected from your visitors helping it abide by GDPR and privacy rules.
*   It is easy to install and set up for pretty much any WordPress site.
*   Works with the Gutenberg Block editor and Classic editor, and should work with any theme.
*   Insert suggestions anywhere in your site with the simple shortcode [rpnnrecommend]. You can adjust how it appears by adding parameters like [rpnnrecommend total="6" thumbnails="false" class="recommendedlist" title="You might like..."] which would suggest six items the visitor might also be interested in, without featured image thumbnails, and assign the CSS class called "recommendedlist" to it.

The FREE version is limited to 30 synapses/paths/links between content, and the charts will only show up to 30 links and 10 items (e.g. pages). It is designed for you to try the plugin and decide if you like it and want to unlock the PRO version, but you can use the free version for as long as you like - there are no time limits. For small sites you may never need to upgrade.

To unlock the PRO version of the plugin, follow the link in the plugin settings. Here are some of the benefits you would receive:

*   Unlimited Neural Net size and number of synapses/paths (within database and your server limitations).
*   Neural Network graph increases from showing 10 nodes to 500, and from 30 synapses/paths/links to 1500.
*   The ability to manually adjust the weight/prominence of links between urls to help force or discourage certain recommended links.
*   Automatically downloads a list of common search engine bots/crawlers to block and prevent them from affecting the neural network.
*   A list of bad IP addresses is automatically downloaded to block any known misbehaving users from affecting the neural net.
*   Email support and help if needed.

This plugin is Copyright 2024, Neil T King.

== Installation ==

From the WordPress dashboard, under "Plugins" choose "Add New Plugin". Click the "Upload Plugin" button and choose this plugin ZIP file.

Alternatively you can UNZIP the plugin folder into your /wp-content/plugins/ folder and upload it to your site. All of the files should be in a folder like /wp-content/plugins/related-posts-neural-network

Activate the plugin through the "Plugins" menu in WordPress.

From the main WordPress dashboard menu, choose "Related Posts Neural Network" and check the options before
switching on "Learning Mode" for it to learn from visitors and build the neural network.

== Screenshots ==

1. A visual representation of the Neural Network available in the WordPress dashboard.
2. A bar graph showing the number of visitor sessions for each URL.
3. A typical way that suggested articles are displayed.

== Frequently Asked Questions ==

= Is there anything I need to do before I install the plugin? =

ALWAYS make a backup of your website before making changes or installing new plugins, just in case. Even better, copy your website to a test server and try out the plugin there first. There should not be any issues and the plugin is easy to uninstall if needed, but it is better to be safe than sorry. As long as you are running WordPress as the content management system driving your website and are able to install custom plugins, it should work. You can try the plugin for free to ensure you are happy before registering it and paying for the PRO features. As WordPress is so flexible and there is so much customisation possible I cannot guarantee it will work on every instance, and cannot be held responsible for any issues that arrise.

= Can it be told to only suggest some content and not others? =

Yes. In the settings for the plugin you can set what must appear in the URL for it to be included in the neural network, for example if you only want it to learn and suggest about blog articles you may want it to only monitor URLs which contain "/article/" or "/blog/". For an online shop, you might want to only monitor and suggest products which all live under "/product/". If there are particular pages or parts of your site that you don't want it to know about then you can add those too. You can even get it to strip out certain variables which appear in the URL such as search terms or ID numbers, or all GET variables. It is quite flexible.

= What about search terms and other variables in the URL? =

You can tell it to strip all GET variables from the URL if you wish, or just certain ones. By default, WordPress tends to add search terms to the URL using an "s" variable so just that can be removed if you like. If you are using permalinks for your URLs and not relying on ID numbers added as GET variables to the URL then I recommend stripping all GET variables.

= Is there a time limit for the free version of the plugin? =

No. You can use the free version for as long as you like but it does have limits on the amount of content and links it will use to build the neural network. This might be enough for smaller sites and should be enough for you to try the plugin and ensure it works fully before you decide if you want to purchase the PRO version. You get some other benefits from registering too, such as the automatic updating and detecting of search engine spiders and bad IP addresses, plus email support if you need it.

= Is the PRO version a subscription service? =

No, there is no subscription needed. Unlocking the PRO features is a one-time fee for the lifetime of the product.

= Will it suggest links on other websites? =

No. The plugin only learns from URLs accessed on your website, and will only form links to other URLs within your website.

= Does the plugin send data to you or share information with anyone else? =

No. It checks in with my server to see if you are a PRO user, but no visitor data or statistics leave your server and nothing is shared with any third parties. Security and privacy is very important.

= What if I change the permalink structure or URLs in my website? = 

The plugin is smart enough to know if pages or posts etc. are no longer there (e.g. deleted) and will not recommend them any more, but it cannot know if something has moved. This is why it is important to ensure your website URLs are formatted how you want them to be before switching on "Learning Mode". You can wipe the Neural Net and start again if needed from the settings area (under the "Reset" tab) but there is no way to undo a reset. The brain will be washed clean!

= Does it use ChatGPT or Google Gemini etc. for it's Artificial Intelligence? =

No. Those are LLMs (Large Language Models) and aren't required for a single, specialised task like this. The machine learning system built for this plugin is unique and entirely self-contained within the plugin so there is no need to use any external services.

= How easy is it to set up? =

Very easy. Once installed you simply need to ensure you enter some key bits of information in the plugin settings area so it doesn't start learning about parts of your website you do not want it to suggest. Once you are happy it will only collect visits for the relevant parts of your website, turn on "Learning Mode" and it will start to make the neural net. The more visitors your site gets, the more links it will create and the more accurate it's suggestions will become. I recommend leaving it learning for a while before getting it to suggest links to visitors. You can see how the neural network is progressing by looking at the "Statistics" tab in the plugin settings. You can leave the learning mode on while it is making suggestions, or turn it off if you feel it has learned enough.

= How do I insert the suggested links? =

Insert the shortcode [rpnnrecommend] where ever you want it to list some suggested links. By default it will show 3 suggestions with thumbnails of their featured image. You can adjust how it appears by adding parameters like [rpnnrecommend total="6" thumbnails="false" class="recommendedlist" title="You might like..."] which would suggest six items the visitor might be interested in, without featured image thumbnails, and assign the CSS class called "recommendedlist" to it. You can insert the shortcode into template files using the PHP command echo do_shortcode("[rpnnrecommend]");

= Why aren't some visits registering and appearing in the graphs?

The first page viewed by new visitors will get stored but will not create a synapse/path as they have not viewed any other page yet. Every visitor session lasts a maximum of 1 hour. If they return to your website after an hour, it classes it as a new session.

If you think visits to multiple pages are not being recognised, your server may have some kind of aggressive caching switched on so the visitor is seeing a cached version of the pages rather than it triggering a new view. Check your caching settings or consult your hosting provider if unsure.

= What is your background and why did you write this plugin? =

I have been writing software for decades and spent 20 years working in education. I have written software for many industries including education, automotive, child safety, medical and gaming. Artificial Intelligence and especially neural networks have been an interest of mine for some time. I have built a few for specialist tasks and as I build websites and cloud based applications I thought a WordPress plugin would be very useful for my clients and anyone else with a WordPress website.
The neural network is based on the model in my book, "Make Independent Computer Games" available from Amazon:
https://www.amazon.co.uk/dp/B0CNMFHZ6Z
https://www.amazon.com/dp/B0CNMFHZ6Z

== Third Party Libraries and Links ==

The plugin uses 2 third party libraries which are included within the plugin so they do not need to be called from external sources (for security). They are only accessed by site administrators when they view the statistics tab inside the plugin settings area. Both of these javascript libraries are obfuscated to make the files smaller and faster to load. They are:
* Chart.js - https://www.chartjs.org - MIT License - Full source available from: https://github.com/chartjs/Chart.js
* Vis.js (network) - https://visjs.org - APACHE 2.0 License - Full source available from: https://github.com/visjs/vis-network

The plugin also talks to my server in order to check if the Pro licensed features should be unlocked and to download updated block-lists for search engine bots and bad IP addresses. The information it passes to the server is:
* A unique ID number for your website (containing no personal or private information)
* The host URL of your website in case the website or plugin gets re-installed so it can retrieve your unique ID number.

Communication with my server occurs once when the plugin is activated, then twice a day. It is also checked when the plugin settings page is accessed. This is purely to unlock the Pro features as soon as possible after purchase.

The Privacy Policy is available at: https://www.neiltking.com/neuralnet/#privacy

== Changelog ==

= v0.0.4 =
* Tested and fully working with WordPress 6.7
* Added fixes to help it work with server-side caching which prevented some page visits being recognised.
* Fixed duplicate bar graph labels due to changes in newest version of Chart.js.

= v0.0.3 =
* WordPress translation support added.
* Chart.js updated to latest version (4.4.4).
* Links to chart.js and vis-network.js source code added.
* Inline javascript now queued.
* Extra sanitization of user settings added.
* Shortcode changed from [recommend] to [rpnnrecommend] to avoid conflictions.
* Information on API call added to README.
* Multiple changes to abide by WordPress.org publication rules.

= v0.0.2 =
* Still in beta testing.
* Added total URLs visited to statistics tab.
* Added database disk space usage to statistics tab.
* Search box added for URLs on statistics tab.
* Ability to delete all references to a URL in the neural net.
* Multiple changes to abide by WordPress.org publication rules.

= v0.0.1 =
* Initial test release.
* Still classed as being tested right now.
