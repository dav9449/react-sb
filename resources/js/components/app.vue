<template>
    <div>
        <h2 class="d-flex justify-content-center">DTR AIRLINE</h2>
        <div class="row full-screen-h">
            <div class="flex-wrap d-flex position-absolute d-flex justify-content-center align-items-center">
                <div class="d-flex justify-content-center col-12 h-4">
                    <input type="file" ref="fileInput" @change="handleFileUpload" accept="text/html">
                    <button @click="uploadFile">Upload and parse roaster</button>
                </div>
                <div class="d-flex justify-content-center col-12 d-flex position-relative">
                    <p>only html files accepted</p>
                </div>
            </div>
           
        </div>
    </div>

</template>
<script>
import moment from 'moment'
export default {
  methods: {
    handleFileUpload() {
      this.selectedFile = this.$refs.fileInput.files[0];
    },
    uploadFile() {
      let formData = new FormData();
      formData.append('file', this.selectedFile);

      // Send formData to your server endpoint using axios or any other HTTP client
      // Example with axios:
      axios.post('/api/upload-and-parse', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      .then(response => {
        console.log(response.data);
        // Handle success response
      })
      .catch(error => {
        console.error(error);
        // Handle error
      });
    }
  },
  data() {
    return {
      selectedFile: null
    };
  }
};
</script>