<template>
  <v-dialog
      v-model="dialog"
      max-width="400px"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
          v-bind="attrs"
          v-on="on"
          text
      >
        Edit
      </v-btn>
    </template>
    <v-card
        dark
    >
      <v-card-title>
        <span class="text-h5">Edit card: {{ card.name }}</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-form ref="editCardForm">
            <v-alert v-for="(error, index) in apiError.formErrors"
                     :key="index"
                     outlined
                     type="error"
            >
              {{ error.property }}: {{ error.message }}
            </v-alert>
            <v-text-field
                v-model="name"
                type="text"
                autocomplete="off"
                label="Name*"
                :rules="[required]"
            ></v-text-field>
            <v-text-field
                v-model="power"
                type="number"
                autocomplete="off"
                label="Power*"
                :rules="[integer]"
            ></v-text-field>
          </v-form>
        </v-container>
        <small>*indicates required field</small>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions>
        <v-btn
            text
            @click="dialog = false"
        >
          Close
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn
            :loading="submitLoading"
            text
            @click="submit"
        >
          Confirm
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import {editCardRequest} from "../api/cards";
import {handleApiError} from "../services/errorHandler";

export default {
  name: "CardEditModal",
  props: {
    card: Object
  },
  data() {
    return {
      apiError: {},
      dialog: false,
      submitLoading: false,
      power: '',
      name: '',
      required: function (v) {
        return !!v || 'Field is required'
      },
      integer: function (v) {
        return !!parseInt(v) || 'Value must be integer'
      }
    }
  },
  methods: {
    async submit() {
      if (!this.$refs.editCardForm.validate()) {
        return
      }
      this.apiError = {}
      this.submitLoading = true
      const response = await editCardRequest(
          {
            id: this.card.id,
            name: this.name,
            power: parseInt(this.power),
          })
          .catch(error => {
            this.apiError = handleApiError(error)
            this.$emit('api-error', this.apiError.apiMessage)
          }).finally(() => {
            this.submitLoading = false
          })
      if (response?.status === 200) {
        this.$emit('card-edited', response.data)
        this.dialog = false
      }
    }
  },
  created() {
    this.name = this.card.name
    this.power = this.card.power
  }
}
</script>

<style scoped>

</style>