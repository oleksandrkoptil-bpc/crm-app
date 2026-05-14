(function () {
    const root = document.querySelector(".widget-shell");

    if (!root) {
        return;
    }

    const form = document.getElementById("ticket-widget-form");
    const messageBox = document.getElementById("widget-message");
    const submitButton = form.querySelector('button[type="submit"]');
    const endpoint = root.dataset.endpoint;
    const token = root.dataset.token;

    const fields = {
        customer_name: form.querySelector('[name="customer_name"]'),
        customer_phone: form.querySelector('[name="customer_phone"]'),
        customer_email: form.querySelector('[name="customer_email"]'),
        subject: form.querySelector('[name="subject"]'),
        message: form.querySelector('[name="message"]'),
        attachments: form.querySelector('[name="attachments"]'),
    };

    const phonePattern = /^\+[1-9]\d{7,14}$/;
    const allowedFileExtensions = [
        "pdf",
        "doc",
        "docx",
        "png",
        "jpg",
        "jpeg",
        "webp",
        "txt",
    ];
    const maxFilesCount = 5;
    const maxFileSize = 5 * 1024 * 1024;

    function setFieldError(name, text) {
        const errorNode = form.querySelector(`[data-error-for="${name}"]`);

        if (errorNode) {
            errorNode.textContent = text || "";
        }
    }

    function clearFieldErrors() {
        Object.keys(fields).forEach((name) => setFieldError(name, ""));
    }

    function showMessage(text, type) {
        messageBox.hidden = false;
        messageBox.textContent = text;
        messageBox.className = `widget-message is-${type}`;
    }

    function clearMessage() {
        messageBox.hidden = true;
        messageBox.textContent = "";
        messageBox.className = "widget-message";
    }

    function validate() {
        clearFieldErrors();

        let hasErrors = false;

        if (!fields.customer_name.value.trim()) {
            setFieldError("customer_name", "Name is required.");
            hasErrors = true;
        }

        if (!phonePattern.test(fields.customer_phone.value.trim())) {
            setFieldError("customer_phone", "Enter a valid phone.");
            hasErrors = true;
        }

        if (fields.customer_email.value.trim()) {
            const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
                fields.customer_email.value.trim(),
            );

            if (!emailValid) {
                setFieldError("customer_email", "Enter a valid email.");
                hasErrors = true;
            }
        }

        if (!fields.subject.value.trim()) {
            setFieldError("subject", "Subject is required.");
            hasErrors = true;
        }

        if (!fields.message.value.trim()) {
            setFieldError("message", "Message is required.");
            hasErrors = true;
        }

        const files = Array.from(fields.attachments.files || []);

        if (files.length > maxFilesCount) {
            setFieldError(
                "attachments",
                `You can upload up to ${maxFilesCount} files.`,
            );
            hasErrors = true;
        }

        files.forEach(function (file) {
            const extension = file.name.includes(".")
                ? file.name.split(".").pop().toLowerCase()
                : "";

            if (file.size > maxFileSize) {
                setFieldError(
                    "attachments",
                    "Each file must be smaller than 5 MB.",
                );
                hasErrors = true;
            }

            if (!allowedFileExtensions.includes(extension)) {
                setFieldError(
                    "attachments",
                    "Only pdf, doc, docx, png, jpg, webp and txt files are allowed.",
                );
                hasErrors = true;
            }
        });

        return !hasErrors;
    }

    form.addEventListener("submit", async function (event) {
        event.preventDefault();
        clearMessage();

        if (!validate()) {
            showMessage(
                "Please fix the highlighted fields and try again.",
                "error",
            );
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = "Sending...";

        try {
            const formData = new FormData();

            formData.append(
                "customer[name]",
                fields.customer_name.value.trim(),
            );
            formData.append(
                "customer[phone]",
                fields.customer_phone.value.trim(),
            );
            formData.append(
                "customer[email]",
                fields.customer_email.value.trim(),
            );
            formData.append("subject", fields.subject.value.trim());
            formData.append("message", fields.message.value.trim());

            Array.from(fields.attachments.files || []).forEach(function (file) {
                formData.append("attachments[]", file);
            });

            const response = await fetch(endpoint, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "X-Widget-Token": token,
                },
                body: formData,
            });

            const payload = await response.json().catch(function () {
                return {};
            });

            if (!response.ok) {
                if (response.status === 422 && payload.errors) {
                    const fieldMap = {
                        "customer.name": "customer_name",
                        "customer.phone": "customer_phone",
                        "customer.email": "customer_email",
                        subject: "subject",
                        message: "message",
                        attachments: "attachments",
                        "attachments.0": "attachments",
                        "attachments.1": "attachments",
                        "attachments.2": "attachments",
                        "attachments.3": "attachments",
                        "attachments.4": "attachments",
                    };

                    Object.entries(payload.errors).forEach(function ([
                        key,
                        messages,
                    ]) {
                        const fieldName = fieldMap[key];

                        if (fieldName) {
                            setFieldError(fieldName, messages[0]);
                        }
                    });
                }

                showMessage(
                    payload.message ||
                        "Something went wrong. Please try again.",
                    "error",
                );
                return;
            }

            form.reset();
            clearFieldErrors();
            showMessage("Your request has been sent successfully.", "success");
        } catch (error) {
            showMessage(
                "Unable to send the request right now. Please try again later.",
                "error",
            );
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = "Send request";
        }
    });
})();
