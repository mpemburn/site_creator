<template>
    <div class="card">
        <div class="card-header">Site Creator</div>
        <div class="card-body">
            <form>
                <div class="box source">
                    <h3>Source</h3>
                    <ul class="wrapper">
                        <li class="form-row">
                            <label for="sourceUrl">URL <span class="required">*</span> </label>
                            <input @blur="testUrl" type="text" ref="sourceUrl" name="sourceUrl" required>
                        </li>
                        <li class="form-row">
                            <label for="home">Home Page<br>(if not index.html)</label>
                            <input type="text" ref="home" name="home">
                        </li>
                    </ul>
                </div>
                <div class="box">
                    <h3>Destination</h3>
                    <ul class="wrapper">
                        <li class="form-row">
                            <label for="db">Database</label>
                            <input @blur="testDb" type="text" ref="db" name="db" required>
                        </li>
                        <li class="form-row">
                            <label for="path">Path to WordPress</label>
                            <input @blur="testPath" type="text" ref="path" name="path">
                        </li>
                        <li class="form-row" v-show="hasThemes">
                            <label for="theme">Theme</label>
                            <select ref="theme">
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
            hasThemes: false
        }
    },
    methods: {
        testDb(event) {
            this.testField('db_test', event.target.value);
        },
        testPath(event) {
            this.hasThemes = false;
            this.testField('path_test', event.target.value);
        },
        testUrl(event) {
            this.testField('url_test', event.target.value);
        },
        testField(endpoint, value) {
            let self = this;
            if (value === '') {
                return;
            }
            axios.get('/' + endpoint + '?value=' + value)
                .then(response => {
                    self.hasThemes = false;
                    console.log(response.data.themes);
                    if (response.data.themes) {
                        self.themes = response.data.themes;
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
