<?php

namespace XF\ConnectedAccount\Provider;

use XF\Entity\ConnectedAccountProvider;
use XF\ConnectedAccount\Http\HttpResponseException;

class Google extends AbstractProvider
{
	public function getOAuthServiceName()
	{
		return 'Google';
	}

	public function getDefaultOptions()
	{
		return [
			'client_id' => '',
			'client_secret' => ''
		];
	}

	public function getOAuthConfig(ConnectedAccountProvider $provider, $redirectUri = null)
	{
		return [
			'key' => $provider->options['client_id'],
			'secret' => $provider->options['client_secret'],
			'scopes' => ['profile', 'email'],
			'redirect' => $redirectUri ?: $this->getRedirectUri($provider)
		];
	}

	public function parseProviderError(HttpResponseException $e, &$error = null)
	{
		$errorDetails = json_decode($e->getResponseContent(), true);
		if (isset($errorDetails['error_description']))
		{
			$e->setMessage($errorDetails['error_description']);
		}
		parent::parseProviderError($e, $error);
	}

	public function getIconUrl()
	{
		return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACUAAAAmCAYAAABDClKtAAABS2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDIgNzkuMTYwOTI0LCAyMDE3LzA3LzEzLTAxOjA2OjM5ICAgICAgICAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIi8+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgo8P3hwYWNrZXQgZW5kPSJyIj8+nhxg7wAAA11JREFUWIXNmL9vUlEYhp/a3sYiUWIiDk1burkYqYHFRSqNkwOVhcREWxMXl9rJgUFNWnWr/QuKGw4EBrsVpJMmNBHjoulQbGJq0DQ0XrVIbR3ugcDt/XGgV+KbEOCec977cPjOd79z4D9UT6cDy+GgHwgBHuCyrrkIfALy3myh+E+hyuGgD5gBIoBPclgFSACL3myh5BhUORz0AAvAlCSImRLArDdbqBwJqhwORoAltL/JCVWAaW+2kDHrcMwGaAlIOwiE8EqXw8GHbUMJoCkHYfQaMWswhCqHg07Ej5US3mxh2qzxUEyJGEq3cYMSWgp4J76PoKUKXydAh6DEKttALoYSaMvcMA+J9PGA1hm3BTKCkomjCjDpzRbydubCM4Q28xkZoBao2oriqxbObPxcGbTqXwTG7fKMAZhPNnHqoRaAe3ubbtTUKAe7vfq+FWCsHfNO1bz6IgB9wyonb3+k9+wvfd/pbgCBmKnaiuIH3uobf7wc5vf706A9WMe7AQTQJ95DRo0nrm2ijKjsvvE+lzG7Mq8a+rShSi7uLtahTFNA//lt+s9vZ3ghZfrqiFBFYKweU/p6qKWjMlFra7UdQX6weSALdQuoIRmorksGysmyRUp1qFWLPv7aitJVsPrqM40b9UDhiXohAq8TEn55iT5+zGc/3wxlaLa+d4r734Ns/XHdQqsKLJWLu20T7JV51aoKqYD4+5SJWhGtLmpouTrE3Z1LbP1xAYQCyWjE7oYSQHa7oNUGlFCjkJ9Tx5j7PoZ6oDQPWAoko1aGdkD1HZGVMnqoxa19Fzcrl1neHTIa4AHSgWS006BfwHqWirm4u9QCpUzUSte3JxLre6esjP3ARiAZDcmSBJJRz/jTz2nsi8fF+oeWylPMQlvl8FosZVgOC697wExvddhz/OsdeqvDZl6lXNw9agglzJzYOPjFq6GefRcD5Tv0/bho5DGei7vzplACbAHtVzqu/p2rHP92o/nSs1zcPdt8wXTbHkhG/9lmtO/XOQa+zNCz78rk4u5Jfbvps28tlppGImF2or2BD/wcfJwBDHc3tgccIsacPOAAeLQWSz00a5Q6ChIryYmtfB6YNVuxbUHVJTJ6J4dmGeD5WiyVlxnQ8fFiIBm1O17cAfKyIP+9/gIvkBKjE2YsSQAAAABJRU5ErkJggg==';
	}
}