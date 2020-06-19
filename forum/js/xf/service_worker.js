"use strict";

self.addEventListener('push', function(event)
{
	if (!(self.Notification && self.Notification.permission === 'granted'))
	{
		return;
	}

	try
	{
		var data = event.data.json();
	}
	catch (e)
	{
		console.warn('Received push notification but payload not in the expected format.', e);
		console.warn('Received data:', event.data.text());
		return;
	}

	if (!data || !data.title || !data.body)
	{
		console.warn('Received push notification but no payload data or required fields missing.', data);
		return;
	}

	data.last_count = 0;

	var options = {
		body: data.body,
		dir: data.dir || 'ltr',
		data: data
	};
	if (data.badge)
	{
		options.badge = data.badge;
	}
	if (data.icon)
	{
		options.icon = data.icon;
	}

	var notificationPromise;

	if (data.tag && data.tag_phrase)
	{
		options.tag = data.tag;
		options.renotify = true;

		notificationPromise = self.registration.getNotifications({ tag: data.tag })
			.then(function(notifications)
			{
				var lastKey = (notifications.length - 1),
					notification = notifications[lastKey],
					count = 0;

				if (notification)
				{
					count = parseInt(notification.data.last_count, 10) + 1;
					options.data.last_count = count;

					options.body = options.body +  ' ' + data.tag_phrase.replace('{count}', count.toString());
				}

				return self.registration.showNotification(data.title, options);
			});
	}
	else
	{
		notificationPromise = self.registration.showNotification(data.title, options);
	}

	event.waitUntil(notificationPromise);
});

self.addEventListener('notificationclick', function(event)
{
	var notification = event.notification;
	
	notification.close();

	if (notification.data.url)
	{
		event.waitUntil(clients.openWindow(notification.data.url));
	}
});
