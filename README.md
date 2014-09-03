dsp-decom-testing
=================

Scripts for aiding with DSP decommissioning testing.

Sandbox - Test compare tool
========================

What is it?
-----------
A Javascript bookmarklet script to compare the URL of the page that you're on (must be a BBC Sport sandbox page) with it's test environment equivalent.

Install
------

```javascript
javascript:(function(){if(window.myBookmarklet!==undefined){myBookmarklet();}else{document.body.appendChild(document.createElement('script')).src='https://rawgithub.com/m0nkmaster/dsp-decom-testing/master/bookmarklet.js?';}})();
```

Copy the above javascript code snippet into a bookmark.

Usage
-----

When you are viewing a BBC Sport page on your sandbox. Click on the bookmarklet and the HTML source of the page will be compared with the source of the equivalent TEST url.

If the two HTML sources match you will be told so. Otherwise you will be offered the option to view a visual diff of the two pieces of code.

Notes
-----

There are a few acceptable differences which the script makes exceptions for:

+ /sport/0/... and /sport/... differences are ignored as we now have rewrites in place to handle this stuff
+ Whitespace differences are largely ignored
+ Sandbox domain differences are ignored
