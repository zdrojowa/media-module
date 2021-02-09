<template>
  <div>
    <upload-file :route="uploadRoute" @upload="upload"></upload-file>
    <b-card>
      <b-card-text class="grid">
        <file
            v-for="(file, i) in files"
            :file="file"
            :i="i"
            :key="i"
            :media-route="mediaRoute"
            :info-route="infoRoute"
            :editor-route="editorRoute"
            :delete-route="deleteRoute"
            @remove="remove"
        ></file>
      </b-card-text>
    </b-card>
  </div>
</template>

<script>
    import File from "./File";
    import UploadFile from "./UploadFile";
    export default {
        props: ['files', 'uploadRoute', 'mediaRoute', 'infoRoute', 'editorRoute', 'deleteRoute'],

        components: {
          File,
          UploadFile
        },

        data() {
            return {
              uploadedFiles: []
            }
        },

        methods: {

          upload(file) {
              this.$emit('upload', file)
          },

          remove(file) {
              this.$emit('remove', file)
          }
        },
    }
</script>

