<template>
  <div>
      <b-modal :id="'modal-image-' + file._id" title="ImageEditor" size="xl" hide-footer>
          <b-form inline>
              <label class="sr-only">Nazwa wersji obrazka</label>
              <b-form-input
                v-model="name"
                type="text"
                placeholder="Nazwa wersji obrazka"
                :state="name.length > 0"
              ></b-form-input>
              <b-button @click="save">Zapisz</b-button>
          </b-form>
          <tui-image-editor
              :options="options"
              :include-ui="useDefaultUI"
              ref="tuiImageEditor"
          ></tui-image-editor>
      </b-modal>
  </div>
</template>

<script>
    import {ImageEditor} from '@toast-ui/vue-image-editor';
    export default {
        props: ['file', 'src', 'route', 'type'],

        components: {
          'tui-image-editor': ImageEditor
        },

        data() {
            return {
              useDefaultUI: true,
              options: {
                cssMaxWidth: 700,
                cssMaxHeight: 1000,
              },
              name: ''
            }
        },

        mounted() {
            this.$root.$on('bv::modal::show', (bvEvent, modalId) => {
                if (modalId === 'modal-image-' + this.file._id) {
                    this.options = {
                        includeUI: {
                            loadImage: {
                                path: this.src,
                                name: this.file.title ? this.file.title : 'Image'
                            },
                        },
                        initMenu: 'icon',
                        cssMaxWidth: 700,
                        cssMaxHeight: 1000,
                        usageStatistics: false
                    }
                    this.name = this.type === 'default' ? '' : this.type
                }
            })
        },

        methods: {
          save() {
            let self = this
            if (self.name) {
              let formData = new FormData()
              formData.append("image", self.dataURItoBlob(self.$refs.tuiImageEditor.invoke('toDataURL')))
              formData.append("name", self.name)
              formData.append("media", self.file._id)
              axios.post(self.route, formData, {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              }).then(function(response) {
                  self.$emit('save', {type: response.data, name: self.name})
                  self.$bvModal.hide('modal-image-' + self.file._id)
              }).catch(function(error) {
                console.log(error)
              })
            }
          },

          dataURItoBlob(dataURI) {
            let byteString;
            if (dataURI.split(',')[0].indexOf('base64') >= 0) {
              byteString = atob(dataURI.split(',')[1])
            } else {
              byteString = unescape(dataURI.split(',')[1])
            }

            let mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

            let ia = new Uint8Array(byteString.length)
            for (let i = 0; i < byteString.length; i++) {
              ia[i] = byteString.charCodeAt(i);
            }

            return new Blob([ia], {type:mimeString})
          }
        },
    }
</script>

