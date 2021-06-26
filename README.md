# Twilio WhatsApp WeatherAPI Chatbot

This is a small Mezzio application which forms the basis of an upcoming tutorial for [the Twilio blog](https://twilio.com/blog) which will show how to create a chatbot using [Twilio's WhatsApp API](https://www.twilio.com/whatsapp), [WeatherAPI](https://www.weatherapi.com/), and [Mezzio](https://docs.mezzio.dev/mezzio/).

If you'd like to learn the essentials of building applications using Mezzio, the best framework for PHP, grab a copy of [Mezzio Essentials](https://mezzioessentials.com)!

## Usage

To build and run the application, follow the steps below:

- Clone the application
- Install the required dependencies
- Add your Weather API key as the value for `WEATHERAPI_API_KEY` to _.env_.
  It was created during the cloning of the project.
- Run the application

You can do most of these by running the commands below.

```bash
git clone https://github.com/settermjd/twilio-whatsapp-weatherapi-chatbot.git
cd twilio-whatsapp-weatherapi-chatbot
composer install
composer serve
```

At this point, the application will be listening on localhost on port 8080, so you're ready to make requests.
To do that, make a POST request with a body that follows the following format, replacing `<city>` with your desired city name.

```
What is the weather like in <city> today?
```

Here's an example of how to do so using cURL.

```bash
curl -X POST --data "Body=What is the weather like in Rockhampton today?" http://localhost:8080
```

If you prefer a GUI application, I highly recommend [Postman](https://www.postman.com/downloads/).

You should see a response similar to the following (formatted to aid readability).

```xml
<Response>
    <Message>
        In Rockhampton (Queensland, Australia), today, it's 13 degrees celsius, but feels like 12, with a humidity of 94 percent. 
The wind is currently 6 kp/h from the SE.
    </Message>
</Response>
```