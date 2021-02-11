<template>
  <div class="column-flex">
      <b-button v-if="link" type="button" :href="src" variant="primary" target="_blank">
          Link
      </b-button>
      <img
          v-if="isImage"
          :src="src"
          :alt="file.alt"
      >
      <b-icon-file-text
          v-else-if="isPdf"
          style="width:100px; height:100px;"
      ></b-icon-file-text>
      <b-embed
          v-else-if="isVideo"
          type="iframe"
          :src="src"
      ></b-embed>
      <b-icon-file
          v-else
          style="width:100px; height:100px;"
      ></b-icon-file>
      <span v-if="show">{{ file.title ? file.title : '' }}</span>
  </div>
</template>

<script>
    export default {
        props: ['file', 'route', 'type', 'show', 'link'],

        computed: {
          isImage() {
            return this.file.mimetype.includes('image')
          },

          isPdf() {
            return this.file.mimetype.includes('pdf')
          },

          isVideo() {
            return this.file.mimetype.includes('video')
          },

          src() {
            return this.route + this.file._id + (this.type !== 'default' ? '-' + (this.type) : '')
          }
        }
    }
</script>

