<template>
    <b-form-group
        :label="label"
    >
        <selector
            :id="id"
            :extensions="extensions"
            @media-selected="select"
            :route="route"
            :media-route="mediaRoute"
        ></selector>

        <div class="grid" v-if="item">
            <div class="column-flex">
                <file-view
                    :file="item.file"
                    :route="mediaRoute"
                    :show="show"
                    :type="item.type"
                    :link="link"
                ></file-view>
                <b-button type="button" @click="remove" variant="danger">
                    <b-icon-trash></b-icon-trash>
                </b-button>
            </div>
        </div>
    </b-form-group>
</template>

<script>
import Selector from './Selector'
import FileView from './FileView'

export default {
    props: ['id', 'extensions', 'route', 'mediaRoute', 'file', 'show', 'link', 'label'],

    components: {
        Selector,
        FileView,
    },

    data() {
        return {
            item: null
        }
    },

    mounted: function() {
        if (this.file) {
            this.getFile(this.file.id)
                .then(response => {
                    if (response && response.data && 0 in response.data) {
                        this.item = {file: response.data[0], type: this.file.type}
                    }
                })
        }
    },

    methods: {
        getFile(id) {
            return axios.get(this.route + '?search=' + id)
                .catch(err => {
                    console.log(err)
                })
        },

        select(file) {
            this.item = file
            this.$emit('media-changed', this.item)
        },

        remove() {
            this.item = null
            this.$emit('media-changed', this.item)
        }
    },

    watch: {
        file() {
            if (this.file) {
                this.getFile(this.file.id)
                    .then(response => {
                        if (response && response.data && 0 in response.data) {
                            this.item = {file: response.data[0], type: this.file.type}
                        }
                    })
            }
        }
    }
}
</script>