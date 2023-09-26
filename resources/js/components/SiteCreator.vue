<template>
    <div class="card">
        <div class="card-header">Site Creator</div>
        <div class="card-body">
            <form>
                <div class="box source">
                    <h3>Source</h3>
                    <ul class="wrapper">
                        <li class="form-row">
                            <label for="sourceUrl">URL <span class="required">*</span></label>
                            <input @keyup="testUrl" type="text" ref="sourceUrl" name="sourceUrl" required>
                            <button @click.prevent="testUrl" class="btn btn-primary btn-sm btn-test valid" :class="isValid.url_test ? 'valid' : 'invalid'">Test</button>
                        </li>
                        <li v-if="isValid.url_test" class="form-row">
                            <label for="home">Home Page</label>
                            <input @keyup="testHome" v-model="homePage" type="text" ref="home" name="home" :disabled=homeDisabled>
                            <button v-if="foundHome" @click.prevent="testHome" class="btn btn-primary btn-sm btn-test" :class="isValid.home_test ? 'valid' : 'invalid'">Test</button>
                            <button v-else @click.prevent="findHome" class="btn btn-primary btn-sm btn-test">Find</button>
                            <img ref="loading" class="loading" v-show="isHomeLoading"
                                 src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" alt=""
                                 width="24"
                                 height="24">
                        </li>
                    </ul>
                </div>
                <div v-if="errorMessage.length > 0" class="text-danger">{{ errorMessage }}</div>
                <div class="box">
                    <h3>Destination</h3>
                    <ul class="wrapper">
                        <li class="form-row">
                            <label for="db">Database <span class="required">*</span></label>
                            <input @keyup="testDb" type="text" ref="db" name="db" required>
                            <button @click.prevent="testDb" class="btn btn-primary btn-sm btn-test" :class="isValid.db_test ? 'valid' : 'invalid'">Test</button>
                        </li>
                        <li class="form-row">
                            <label for="path">Path to WordPress</label>
                            <input @keyup="testPath" type="text" ref="path" name="path">
                            <button @click.prevent="testPath" class="btn btn-primary btn-sm btn-test" :class="isValid.path_test ? 'valid' : 'invalid'">Test</button>
                        </li>
                        <li class="form-row" v-show="hasThemes">
                            <label for="theme">Theme</label>
                            <select ref="theme" class="shift-1rem">
                                <option value="">Select</option>
                                <option v-for="theme in themes" :value="theme.name">
                                    {{ theme.label }}
                                </option>
                            </select>
                        </li>
                        <li class="form-row">
                            <button @click.prevent="createSite" class="btn btn-primary btn-sm" :disabled="! canSubmit">Submit</button>
                        </li>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import {end} from "@popperjs/core";

export default {
    data() {
        return {
            themes: [],
            hasThemes: false,
            homeDisabled: true,
            homePage: '',
            errorMessage: '',
            isValid: {
                db_test: false,
                home_test: false,
                path_test: false,
                url_test: false,
            },
            isHomeLoading: false,
            foundHome: false,
            canSubmit: false,
        }
    },
    watch: {
        isValid: {
            deep: true,
            handler: function (tests) {
                this.canSubmit = true;
                Object.entries(tests).forEach(([key, value]) => {
                    this.canSubmit = this.canSubmit & value;
                })
                console.log(this.canSubmit)
            }
        }
    },
    methods: {
        testDb(event) {
            if (event.type === 'keyup' && ! this.isValid.db_test) {
                return;
            }
            let db = this.$refs.db.value;
            this.testField('db_test', db);
        },
        testPath(event) {
            if (event.type === 'keyup' && ! this.isValid.path_test) {
                return;
            }
            this.hasThemes = false;
            let path = this.$refs.path.value;
            this.testField('path_test', path);
        },
        testUrl(event) {
            if (event.type === 'keyup' && ! this.isValid.url_test) {
                return;
            }
            let url = this.$refs.sourceUrl.value;
            this.testField('url_test', url);
        },
        findHome(event) {
            let url = this.$refs.sourceUrl.value;
            this.isHomeLoading = true;
            this.testField('find_home', url + '/' + event.target.value);
        },
        testHome(event) {
            if (event.type === 'keyup' && ! this.isValid.home_test) {
                return;
            }
            let url = this.$refs.sourceUrl.value;
            let page = this.$refs.home.value;
            this.testField('home_test', url + '/' + page);
        },
        testField(endpoint, value) {
            let self = this;
            this.errorMessage = '';
            //self.homeDisabled = true;

            if (value === '') {
                return;
            }
            axios.get('/' + endpoint + '?value=' + value)
                .then(response => {
                    console.log(response.data);
                    let data = response.data;

                    // Flag the current test as success if it is.
                    self.isValid[endpoint] = data.success;

                    if (! data.success) {
                        self.errorMessage = data.message;
                        return;
                    }

                    if (data.home) {
                        self.homePage = data.home;
                        self.homeDisabled = false;
                        self.isHomeLoading = false;
                        self.foundHome = true;
                        self.isValid.home_test = true;
                    }
                    // Populate and show themes dropdown if found
                    if (data.themes && data.themes.length !== 0) {
                        self.themes = data.themes;
                        self.hasThemes = true;
                    }
                });
        },
        createSite() {
            axios.post('/create_site', {
                url: this.$refs.sourceUrl.value,
                home: this.$refs.home.value,
                db: this.$refs.db.value,
                path: this.$refs.path.value,
                theme: this.$refs.theme.value,
            }).then(response => {
                console.log(response);
            });

        }
    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>
