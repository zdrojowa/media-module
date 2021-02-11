<template>
    <div>
        <b-form-file
          v-model="files"
          multiple
          placeholder="Upuść tutaj pliki aby je dodać"
          browse-text="Dodaj pliki"
        ></b-form-file>
        <b-progress v-if="percentage > 0" max="100" :value="percentage"></b-progress>
    </div>
</template>

<script>
    export default {
        props: ['route'],

        data() {
            return {
                files: [],
                percentage: 0
            }
        },

        watch: {
            files() {
              let self = this
              this.files.forEach(file => {
                    let formData = new FormData()
                    formData.append("file", file)
                    axios.post(this.route, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function( progressEvent ) {
                            self.percentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ))
                        }
                    }).then(function(response) {
                        self.$emit('upload', response.data)
                        self.percentage = 0;
                    }).catch(function(error) {
                        console.log(error)
                    })
                })
            }
        }
    }
</script>

