<template>
    <div>
        <b-button v-b-modal="'modal-selector'" variant="primary">Wybierz</b-button>
        <b-modal id="modal-selector" title="Wybierz plik" size="xl" hide-footer>
            <template #modal-title>
                <b-form-input type="text" placeholder="Szukaj" v-model="search" @change="find"/>
            </template>
            <div class="grid">
                <div class="column-flex" v-for="file in files">
                    <file-view
                        :file="file"
                        :route="mediaRoute"
                        :show="true"
                        type="default"
                        @click.native="confirm(file)"
                        :link="false"
                    ></file-view>
                </div>
            </div>
        </b-modal>
        <b-modal id="modal-selector-2" title="Potwierdź wybranie" cancel-title="Nie" ok-title="Tak" @cancel="cancel" @ok="choose">
            <b-form-select
                v-if="isImage"
                v-model="type"
                :options="types"
                class="mb-3">
            </b-form-select>
            <span v-else>Czy jesteś pewny?</span>
        </b-modal>
    </div>
</template>

<script>
    import FileView from "./FileView";
    export default {
        props : {
            extensions: {
                type: String
            },
            route: {
                type: String
            },
            mediaRoute: {
                type: String
            }
        },

        components: {
            FileView
        },

        data() {
            return {
                search: '',
                files: [],
                file: null,
                type: 'default',
                types: []
            }
        },

        mounted: function() {
            this.find()
        },

        computed: {
            isImage() {
                return this.file && this.file.mimetype.includes('image') && !this.file.mimetype.includes('svg')
            },
        },

        methods: {
            find() {
                let self = this
                axios.get(this.route + '?extensions=' + self.extensions + '&search=' + self.search)
                .then(function(response) {
                    self.files = response.data
                }).catch(function(error) {
                    console.log(error)
                })
            },

            confirm(file) {
                this.file      = file
                this.fileTypes = this.file.types
                this.types     = []

                if (this.isImage) {
                    this.type = 'default'
                    this.types.push({text: 'Domyślny', value: 'default'})
                    Object.keys(this.fileTypes).forEach(type => {
                        this.types.push({text: type, value: type})
                    })
                }
                this.$bvModal.show('modal-selector-2')
            },

            cancel() {
                this.file = null
            },

            choose() {
                this.$emit('media-selected', {file: this.file, type: this.type})
            }
        },

        watch: {
            extensions() {
                this.find();
            }
        }
    }
</script>
