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
                            <input type="text" ref="sourceUrl" name="sourceUrl" required>
                            <button @click.prevent="testUrl" class="btn btn-primary btn-sm btn-test">Test</button>
                        </li>
                        <li class="form-row">
                            <label for="home">Home Page<br>(if not index.html)</label>
                            <input v-model="homePage" type="text" ref="home" name="home" :disabled=homeDisabled>
                            <button @click.prevent="findHome" class="btn btn-primary btn-sm btn-test">Find</button>
                            <button @click.prevent="testHome" class="btn btn-primary btn-sm btn-test">Test</button>
                            <img ref="loading" class="loading" v-show="isHomeLoading"
                                 src="https://cdnjs.cloudflare.com/ajax/libs/galleriffic/2.0.1/css/loader.gif" alt="" width="24"
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
                            <input type="text" ref="db" name="db" required>
                            <button @click.prevent="testDb" class="btn btn-primary btn-sm btn-test">Test</button>
                        </li>
                        <li class="form-row">
                            <label for="path">Path to WordPress</label>
                            <input type="text" ref="path" name="path">
                            <button @click.prevent="testPath" class="btn btn-primary btn-sm btn-test">Test</button>
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
                            <button @click="createSite" class="btn btn-primary btn-sm">Submit</button>
                        </li>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            themes: [],
            hasThemes: false,
            homeDisabled: true,
            homePage: '',
            errorMessage: '',
            isHomeLoading: false
        }
    },
    methods: {
        testDb(event) {
            let db = this.$refs.db.value;
            this.testField('db_test', db);
        },
        testPath(event) {
            this.hasThemes = false;
            let path = this.$refs.path.value;
            this.testField('path_test', path);
        },
        testUrl(event) {
            let url = this.$refs.sourceUrl.value;
            this.testField('url_test', url);
        },
        findHome(event) {
            let url = this.$refs.sourceUrl.value;
            this.isHomeLoading = true;
            this.testField('find_home', url + '/' + event.target.value);
        },
        testHome(event) {
            let url = this.$refs.sourceUrl.value;
            let page = this.$refs.home.value;
            this.testField('home_test', url + '/' + page);
        },
        testField(endpoint, value) {
            let self = this;
            this.errorMessage = '';
            self.homeDisabled = true;

            if (value === '') {
                return;
            }
            axios.get('/' + endpoint + '?value=' + value)
                .then(response => {
                    console.log(response.data);
                    let data = response.data;

                    if (! data.success) {
                        self.errorMessage = data.message;
                    }
                    if (data.home) {
                        self.homePage = data.home;
                        self.homeDisabled = false;
                        self.isHomeLoading = false;
                    }
                    // Populate and show themes dropdown if found
                    if (data.themes && data.themes.length !== 0) {
                        self.themes = data.themes;
                        self.hasThemes = true;
                    }
                });
        },
        createSite() {

        }
    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>
