# quid-wordpress
Wordpress Plugin

At the moment, the user will make two posts:
1. Is public and has the article snippet as well as the button shortcode.
2. Will be private and contain the rest of the article (Not including the snippet) because on the frontend it's element.innerHTML = `${element.innerHTML} ${additionContent}`, we can change this later.

The id of the element in which the additional content will be appended must be the same as the target attribute given to the quidButton shortcode.

The Private Post's title must be the same as the title attribute of the button shortcode.

The flow is as follows:

1. The user loads the merchant's article page, this is the public page.
2. The user clicks pay and when it succeeds, the callback that gets passed the response data is that which is specified as successCallback within requestPayment in the plugin.
3. The callback gets the response and posts it to the wordpress server.
4. Server hashes the response (including their api secret, minus the sig field) and compares it with the sig field in the response, if they match our plugin uses the response's productID field to query the additional content and send it back to the user.

API keys need to be added at the bottom of the Settings/General page.

Example Shortcode: [quidButton price="0.10" url="/when-darkness-overspreads-my-eyes/" id="udew7hui8" name="It Wasn't a Dream" description="Franz' article on News Post York" target="wasntADream" title="IT WASN'T A DREAM"]
