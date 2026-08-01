const API = {

    async request(url, method = "GET", data = null) {

        const options = {
            method,
            headers: {
                "Content-Type": "application/json"
            }
        };

        if (data !== null) {
            options.body = JSON.stringify(data);
        }

        try {

            const response = await fetch(url, options);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            return await response.json();

        } catch (error) {

            console.error(error);

            return {
                success: false,
                message: error.message
            };

        }

    }

};