# quid-wordpress
Wordpress Plugin

The html file will be on the frontend, wordpress has HTML widgets that you can place on the page in which you can write HTML.

We can use a shortcode to remove some of the JS from the user's side.
Using a shortcode they'd see something like [quid-init] and wordpress would render our code in it's place at runtime.
We can also pass in values such as [quid-init apiKey="kt-7387hhd7df3783hbd3" productID="awesome article name"].

At the moment, the user will make two posts:
1. Is public and has the article snippet as well as all the JS and the button.
2. Will be public and will contain the rest of the article (Not including the snippet) because on the frontend it's element.innerHTML = `${element.innerHTML} ${additionContent}`, we can change this later.

The Private Post's title must be the same as the quid-product-id attribute on the button.

The flow is as follows:

1. The user loads the merchant's article page, this is the public page.
2. The user clicks pay and when it succeeds, the callback that gets passed the response data is that which is specified as onPaymentSuccess within autoInit.
3. The callback gets the response and posts it to the server.
4. Server hashes the response (including their api secret, minus the sig field) and compares it with the sig field in the response, if they match our plugin uses the response's productID field to query the additional content and send it back to the user.
