<template>
  <v-dialog
      v-model="dialog"
      max-width="400px"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
          text
          dark
          color="teal accent-2"
          outlined
          v-bind="attrs"
          v-on="on"
      >
        <v-icon
            dark
            left
        >
          mdi-plus
        </v-icon>
        add new
      </v-btn>
    </template>
    <v-card
        dark
    >
      <v-card-title>
        <span class="text-h5">Add new card:</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-form ref="addCardForm">
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
                :rules="[integer, required]"
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
import {addCardRequest} from "../api/cards";
import {handleApiError} from "../services/errorHandler";

export default {
  name: "CardAddModal",
  data() {
    return {
      apiError: {},
      submitLoading: false,
      dialog: false,
      name: '',
      power: '',
      required: function (v) {
        return !!v?.trim() || 'Field is required'
      },
      integer: function (v) {
        return !!parseInt(v) || 'Value must be integer'
      },
    }
  },
  methods: {
    async submit() {
      if (!this.$refs.addCardForm.validate()) {
        return
      }
      this.apiError = {}
      this.submitLoading = true
      const response = await addCardRequest(
          {
            name: this.name,
            power: parseInt(this.power)
          })
          .catch(error => {
            this.apiError = handleApiError(error)
            this.$emit('api-error', this.apiError.apiMessage)
          })
          .finally(() => {
            this.submitLoading = false
          })
      if (response?.status === 201) {
        this.$refs.addCardForm.reset()
        this.$emit('card-added', response.data)
        this.dialog = false
      }
    }
  }
}
</script>

<style scoped>

</style>