![Bluesky Embed Widget](social-preview.png)

# bsky-embed
embed your bluesky posts in a wp widget

To use:

1. Plop the bsky-widget.php file in your plugins folder and activate.
2. appearance > widgets and set the widget on the appropriate sidebar
3. fill out the widget form and press save.
4. enjoy

New in version 1.1:  Shortcode and dark mode support!
[bsky_embed username="vestrainteractive.com" limit="4" height="150" mode="dark"]

Version 1.1 and later does not need the truncate.js file.  We have left it here for legacy reasons.  In the final release zip, it will not be included.
You will likely need to style the output using CSS.



Inspired by DavidC @twowheelsin.com

GDPR INFO:
The Bluesky Embed Widget plugin, in its current form, is partially GDPR compliant — but with some caveats:

✅ What’s Safe
The plugin does not collect or store any personal data on your WordPress site.

It doesn’t set its own cookies or track users directly.

⚠️ What You Need to Consider for GDPR
Third-party Script (Bluesky Embed)
The plugin loads this script: https://cdn.jsdelivr.net/npm/bsky-embed/dist/bsky-embed.es.
and embeds Bluesky posts via a <bsky-embed> web component.

Potential concerns:
Bluesky may load assets or media from their own servers (e.g., images, avatars, videos).
These may expose visitor IP addresses or result in embedded tracking (e.g., cookies, analytics, CDN logs).

No Consent Mechanism
The plugin does not check for or wait on user consent before loading the embed script.
This could violate GDPR and/or ePrivacy Directive requirements in some regions unless:
Consent has been obtained in advance (e.g., via a CMP like Cookiebot or Complianz).
The embed is deferred until the user agrees.
