=== Starter Plugin ===
Contributors:
Requires at least: 4.0.0
Stable tag: 1.0.0

== Description ==

Creates a custom post type and shows posts with ajax load more option.

== Usage ==

-Upon install and active you will get a custom post type (News) created. You can change the name in classes -> class-dc-wpnews-post-type.php

-Shows in template via shortcode. Ex. '[dc-wpnews post_type="news" post_count="6" loadmore_text="Load more"]'
 By default you can use: post_type="news" which is created upon activation. You can change it if you want to show other posts.

-View can be modified in "classes->class-dc-wpnews-views.php". Modify the HTML code in function “get_posts_ajax()”.

-Also have the options to add a dedicated settings page in admin. You can enable it in "class-dc-wpnews-admin.php" by uncommenting the line in function “register_settings_screen()”.
 Then you can also modify setting fields in "class-dc-wpnews-settings.php"

-Addthis sharing option is used. Addthis plugin is not included here. Those code in "classes->class-dc-wpnews-views.php" should be removed if not using.

-CSS styles for the loader animation:
<css code>
.loadmore-button .loader {
    border: 16px solid #f3f3f3; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</css code>