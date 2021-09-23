<template>
  <v-dialog
      v-model="dialog"
      max-width="400px"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
          text
          v-bind="attrs"
          v-on="on"
      >
        Remove
      </v-btn>
    </template>
    <v-card
        dark
    >
      <v-card-title>
        <span class="text-h5">Remove card: {{ card.name }}</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-form ref="removeCardForm">
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
                label="Type name to confirm"
                :rules="[matchName]"
            ></v-text-field>
          </v-form>
        </v-container>
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
import {removeCardRequest} from "../api/cards";
import {handleApiError} from "../services/errorHandler";

export default {
  name: "CardRemoveModal",
  props: {
    card: Object
  },
  data() {
    return {
      apiError: {},
      name: '',
      submitLoading: false,
      dialog: false,
      matchName: (v) => {
        return v === this.$props.card.name || 'Card name does not match'
      },
    }
  },
  methods: {
    async submit() {
      if (!this.$refs.removeCardForm.validate()) {
        return
      }
      this.apiError = {}
      this.submitLoading = true
      const response = await removeCardRequest(this.card.id)
          .catch(error => {
            this.errors = handleApiError(error)
            this.$emit('api-error', this.errors.apiMessage)
          })
          .finally(() => {
            this.submitLoading = false
          })
      if (response?.status === 204) {
        this.$emit('card-removed', this.card)
        this.dialog = false
      }
    }
  },
}
</script>

<style scoped>

</style>