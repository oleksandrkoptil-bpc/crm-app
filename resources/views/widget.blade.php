<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form</title>
    <link rel="stylesheet" href="{{ asset('css/widget.css') }}">
</head>
<body>
    <section
        class="widget-shell"
        data-endpoint="{{ url('/api/tickets') }}"
        data-token="{{ $widgetToken }}"
    >
        <div class="widget-card">
            <div class="widget-copy">
                <span class="widget-eyebrow">Support</span>
                <h1>Send us a message</h1>
            </div>

            <form class="widget-form" id="ticket-widget-form" novalidate>
                <div class="field">
                    <label for="customer_name">Name</label>
                    <input id="customer_name" name="customer_name" type="text" autocomplete="name">
                    <div class="field-error" data-error-for="customer_name"></div>
                </div>

                <div class="field">
                    <label for="customer_phone">Phone</label>
                    <input id="customer_phone" name="customer_phone" type="text" placeholder="+380501112233" autocomplete="tel">
                    <div class="field-error" data-error-for="customer_phone"></div>
                </div>

                <div class="field">
                    <label for="customer_email">Email</label>
                    <input id="customer_email" name="customer_email" type="email" autocomplete="email">
                    <div class="field-error" data-error-for="customer_email"></div>
                </div>

                <div class="field">
                    <label for="subject">Subject</label>
                    <input id="subject" name="subject" type="text">
                    <div class="field-error" data-error-for="subject"></div>
                </div>

                <div class="field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5"></textarea>
                    <div class="field-error" data-error-for="message"></div>
                </div>

                <div class="field">
                    <label for="attachments">Files</label>
                    <input
                        id="attachments"
                        name="attachments"
                        type="file"
                        multiple
                        accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.webp,.txt"
                    >
                    <div class="field-hint">Up to 5 files, 5 MB each. Allowed: pdf, doc, docx, png, jpg, webp, txt.</div>
                    <div class="field-error" data-error-for="attachments"></div>
                </div>

                <div class="widget-message" id="widget-message" hidden></div>

                <button class="widget-submit" type="submit">Send request</button>
            </form>
        </div>
    </section>

    <script src="{{ asset('js/widget.js') }}"></script>
</body>
</html>
