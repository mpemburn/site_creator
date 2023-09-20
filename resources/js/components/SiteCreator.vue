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
                            <input type="text" ref="sourceUrl" name="sourceUrl" required>
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
                            <input type="text" ref="db" name="db" required>
                        </li>
                        <li class="form-row">
                            <label for="db">Path to WordPress</label>
                            <input type="text" ref="db" name="db">
                        </li>
                        <li class="form-row">
                            <label for="prefix">Table Prefix</label>
                            <input type="text" ref="prefix" name="prefix" placeholder="wp_">
                        </li>
                        <li class="form-row">
                            <label for="theme">Theme</label>
                            <input type="text" ref="theme" name="theme" required>
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
    methods: {
        createSite() {

        }
    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>
