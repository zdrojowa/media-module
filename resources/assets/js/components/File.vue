<template>
  <div class="column-flex">
      <b-button v-b-modal="'modal-' + file._id">Szczegóły</b-button>
      <file-view :file="file" :route="mediaRoute" :show="false" type="default" :link="false"></file-view>
      <b-button variant="danger" @click="remove">Usuń</b-button>
      <b-modal :id="'modal-' + file._id" title="Szczegóły załączonego pliku" size="xl" hide-footer>
        <b-row>
          <b-col>
            <b-form-select
                v-if="isImage"
                v-model="type"
                :options="types"
                class="mb-3">
            </b-form-select>
          </b-col>
          <b-col>
              <b-button v-if="version" variant="danger" @click="removeVersion">Usuń wersję</b-button>
          </b-col>
          <b-col v-if="isImage">
            <b-button v-b-modal="'modal-image-' + file._id">ImageEditor</b-button>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
              <file-view :file="file" :route="mediaRoute" :show="false" :type="type" :link="true"></file-view>
          </b-col>
          <b-col>
            <p>
              <strong>Identyfikator pliku:</strong> {{ file._id }}
            </p>
            <p>
              <strong>Typ pliku:</strong> {{ mimetype }}
            </p>
            <p>
              <strong>Wysłany na serwer:</strong> {{ file.created_at | moment('LL') }}
            </p>
            <p>
              <strong>Rozmiar pliku:</strong> {{ sizeString }}
            </p>
            <b-form>
              <b-form-group
                  label="Tekst alternatywny"
              >
                <b-form-input
                    v-model="alt"
                    type="text"
                    placeholder="Tekst alternatywny"
                    @change="change('alt')"
                    :state="hasChanged.alt"
                ></b-form-input>
              </b-form-group>
              <b-form-group
                  label="Tytuł"
              >
                <b-form-input
                    v-model="title"
                    type="text"
                    placeholder="Tytuł"
                    @change="change('title')"
                    :state="hasChanged.title"
                ></b-form-input>
              </b-form-group>
              <b-form-group
                  label="Opis"
              >
                <b-form-input
                    v-model="description"
                    type="text"
                    placeholder="Opis"
                    @change="change('description')"
                    :state="hasChanged.description"
                ></b-form-input>
              </b-form-group>
            </b-form>
          </b-col>
        </b-row>
      </b-modal>

    <image-editor v-if="isImage" :file="file" :src="src" :route="editorRoute" :type="type" @save="save"></image-editor>
  </div>
</template>

<script>
    import ImageEditor from "./ImageEditor";
    import FileView from "./FileView";
    export default {
        props: ['file', 'i', 'mediaRoute', 'infoRoute', 'editorRoute', 'deleteRoute'],

        components: {
          ImageEditor,
          FileView
        },

        data() {
            return {
              type: 'default',
              types: [],
              alt: '',
              title: '',
              description: '',
              fileTypes: [],
              hasChanged: {
                  alt: null,
                  title: null,
                  description: null,
              }
            }
        },

        mounted() {
          this.alt = this.file.alt
          this.title = this.file.title
          this.description = this.file.description
          this.fileTypes = this.file.types

          if (this.isImage) {
              this.type = 'default'
              this.types.push({text: 'Domyślny', value: 'default'})
              Object.keys(this.fileTypes).forEach(type => {
                this.types.push({text: type, value: type})
              })
          }
        },

        computed: {
          isImage() {
            return this.file.mimetype.includes('image')
          },

          src() {
            let src = this.mediaRoute + this.file._id
            if (this.type !== 'default') {
              src += '-' + this.type
            }
            return src
          },

          mimetype() {
            let type = this.file.mimetype
            if (this.type !== 'default') {
              type = this.fileTypes[this.type].mimetype
            }
            return type
          },

          size() {
            let size = this.file.size
            if (this.type !== 'default') {
              size = this.fileTypes[this.type].size
            }
            return size
          },

          sizeString() {
            let sizes = ['Bitów', 'KB', 'MB', 'GB', 'TB'];
            let sizeStr = ''
            if (this.size === 0) {
              sizeStr = '0 Bitów'
            } else {
              let i = parseInt(Math.floor(Math.log(this.size) / Math.log(1024)));
              sizeStr = Math.round(this.size / Math.pow(1024, i), 2) + ' ' + sizes[i];
            }
            return sizeStr
          },

          version() {
            return this.type !== 'default' && this.type !== 'dashboardthumbnail'
          },
        },

        methods: {
          change(property) {
            let self = this
            let route = this.infoRoute.replace('id', this.file._id)
            axios.put(route, {
              'attribute': property,
              'value': self[property]
            }).then(function(response) {
              self.hasChanged[property] = true
            }).catch(function(error) {
              console.log(error)
              self.hasChanged[property] = false
            })
          },

          save(img) {
              if (!(img.name in this.fileTypes)) {
                  this.types.push({text: img.name, value: img.name})
              }
              this.fileTypes[img.name] = img.type
          },

          remove() {
              let self = this
              self.$bvModal.msgBoxConfirm('Czy jesteś pewny?', {
                  title: 'Usunięcie miedia',
                  size: 'sm',
                  buttonSize: 'sm',
                  okVariant: 'danger',
                  okTitle: 'Tak',
                  cancelTitle: 'Nie',
                  footerClass: 'p-2',
                  hideHeaderClose: false,
                  centered: true
              })
              .then(value => {
                  if (value) {
                      axios.post(self.deleteRoute + self.file._id)
                      .then(function(response) {
                          self.$emit('remove', self.i)
                      }).catch(function(error) {
                          console.log(error)
                      })
                  }
              })
              .catch(error => {
                  console.log(error)
              })
          },

          removeVersion() {
                let self = this
                self.$bvModal.msgBoxConfirm('Czy jesteś pewny?', {
                    title: 'Usunięcie wersji miedia',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'danger',
                    okTitle: 'Tak',
                    cancelTitle: 'Nie',
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                })
                .then(value => {
                    if (value) {
                        axios.post(self.deleteRoute + self.file._id + '-' + self.type)
                            .then(function(response) {
                                delete self.fileTypes[self.type]
                                let i = self.types.findIndex(element => element.text === self.type)
                                self.types.splice(i, 1)
                            }).catch(function(error) {
                            console.log(error)
                        })
                    }
                })
                .catch(error => {
                    console.log(error)
                })
          }
        },
    }
</script>

