/**
 * Encapsulate server validation errors on forms.
 */
export class Errors {
    /**
     * Create a new Errors instance.
     */
    constructor() {
        this.errors = {};
    }

    /**
     * Determine if an errors exists for the given field.
     *
     * @param {string} field
     */
    has(field) {
        return this.errors.hasOwnProperty(field);
    }

    /**
     * Determine if we have any errors.
     */
    hasAny() {
        return Object.keys(this.errors).length > 0;
    }

    /**
     * Retrieve the error message for a field.
     *
     * @param {string} field
     */
    get(field) {
        if (this.errors[field])
            return this.errors[field].join(' ');
    }

    /**
     * Record the new errors.
     *
     * @param {object} errors
     */
    record(errors) {
        // Treat Laravel errors for arrays, i.e,
        // convert {field.0:... , field.1:... , ...} in {field: [0:... , 1:..., ...}
        for (let field in errors) {
            if (field.indexOf('.') > -1) {
                let parts = field.split('.');
                let name = parts.shift();
                if (!errors.hasOwnProperty(name))
                    errors[name] = [];
                errors[name][parts] = errors[field];
                delete errors[field];
            }
        }

        this.errors = errors;
    }

    /**
     * Clear one or all error fields.
     *
     * @param {string|null} field
     */
    clear(field) {
        if (field) {
            delete this.errors[field];
            return;
        }

        this.errors = {};
    }
}

/**
 * A Form has data to be sent and response errors. It makes easy to
 * send ajax requests and treat errors. It bounds easily with Vue components.
 */
export default class Form {
    /**
     * Create a new Form instance.
     *
     * @param {object} data
     */
    constructor(data) {
        this.originalData = data;
        this.errors = new Errors();
        this._isLoading = false;

        for (let field in data) {
            this[field] = data[field];
        }
    }

    /**
     * Fetch all relevant data for the form.
     */
    data() {
        let data = {};

        for (let property in this.originalData) {
            data[property] = this[property];
        }

        return data;
    }

    /**
     * Reset the form fields.
     */
    reset() {
        for (let field in this.originalData) {
            this[field] = '';
        }

        this.errors.clear();
    }

    /**
     * Send a POST request to the given URL.
     *
     * @param {string} url
     */
    post(url) {
        return this.submit('post', url);
    }

    /**
     * Send a PUT request to the given URL.
     * .
     * @param {string} url
     */
    put(url) {
        return this.submit('put', url);
    }

    /**
     * Send a PATCH request to the given URL.
     * .
     * @param {string} url
     */
    patch(url) {
        return this.submit('patch', url);
    }

    /**
     * Send a DELETE request to the given URL.
     * .
     * @param {string} url
     */
    delete(url) {
        return this.submit('delete', url);
    }

    /**
     * Submit the form.
     *
     * @param {string} requestType
     * @param {string} url
     */
    submit(requestType, url) {
        this._isLoading = true;

        return new Promise((resolve, reject) => {
            axios[requestType](url, this.data())
                .then(response => {
                    this.onSuccess(response.data);

                    resolve(response.data);
                })
                .catch(error => {
                    this.onFail(error.response.data);

                    reject(error.response);
                });
        });
    }

    /**
     * Return whether the form is loading the response.
     *
     * @returns {boolean}
     */
    isLoading() {
        return this._isLoading;
    }

    /**
     * Handle a successful form submission.
     *
     * @param {object} data
     */
    onSuccess(data) {
        this._isLoading = false;
    }


    /**
     * Handle a failed form submission.
     *
     * @param {object} errors
     */
    onFail(errors) {
        this._isLoading = false;
        this.errors.record(errors);
    }
}

/**
 * Helper functions to use in a Vue Component. It expects a 'form' data field.
 */
export let FormMixin = {
    methods: {
        /**
         * Return the type of the Buefy <b-field> according to error presence.
         */
        fieldType($name) {
            return this.form.errors.has($name) ? 'is-danger' : '';
        },

        errorMessage($name) {
            return this.form.errors.get($name);
        },

        clearError($name) {
            this.form.errors.clear($name);
        }
    }
};