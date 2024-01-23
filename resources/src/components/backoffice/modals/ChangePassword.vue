<template>
    <b-modal :id="props.id || 'change-password-modal'" ok-variant="dark" @ok="props.ok" scrollable :title="t('edit_password')">
      <b-form :id="(props.id || 'change-password-modal') + '-password_data'">
        <div v-if="props.hideOldPassword == false">
            <form-group-input
              :id="(props.id || 'change-password-modal') + '-old_password'"
              type="password"
              :label="t('old_password')"
              :description="t('backoffice.input.old_password.description')"
              :validFeedback="t('backoffice.input.old_password.valid_feedback')"
              :invalidFeedback="t('backoffice.input.old_password.invalid_feedback')"
              :inputValue="old_password"
              v-model="old_password"
            >
            </form-group-input>
        </div>
        <form-group-input
          :id="(props.id || 'change-password-modal') + '-password'"
          type="password"
          :label="t('password')"
          :description="t('backoffice.input.password.description')"
          :validFeedback="t('backoffice.input.password.valid_feedback')"
          :invalidFeedback="t('backoffice.input.password.invalid_feedback')"
          :inputValue="password"
          v-model="password"
          :state="new_password_state"
        >
        </form-group-input>
        <form-group-input
          :id="(props.id || 'change-password-modal') + '-confirm_password'"
          type="password"
          :label="t('confirm_password')"
          :description="t('backoffice.input.confirm_password.description')"
          :validFeedback="t('backoffice.input.confirm_password.valid_feedback')"
          :invalidFeedback="
            t('backoffice.input.confirm_password.invalid_feedback')
          "
          :inputValue="confirm_password"
          v-model="confirm_password"
          :state="new_password_state"
        >
        </form-group-input>
      </b-form>
    </b-modal>
  </template>

  <script setup>
  import { ref, watch } from "vue";
  import { useI18n } from "vue-i18n";
  import Utils from "./../../../../js/utilities/utils.js";
  const { t, locale } = useI18n();
  import FormGroupInput from "../../../components/FormGroupInput.vue";
  const first_name_value = ref("");
  const last_name_value = ref("");
  const email_value = ref("");
  const password = ref("");
  const confirm_password = ref("");
  const old_password = ref("");
  const new_password_state = ref(null);
  const emit = defineEmits([
    'update-old-password',
    'update-password',
    'update-confirm-password'
  ])

  const props = defineProps(
    {
        ok: Function,
        hideOldPassword: {
            type: Boolean,
            default: false
        },
        id: String
    }
  )

  const updateOldPassword = () => {
    emit('update-old-password', old_password.value)
  }
  const updatePassword = (newValue) => {
    emit('update-password', password.value)
  }
  const updateConfirmPassword = (newValue) => {
    emit('update-confirm-password', confirm_password.value)
  }

  watch([old_password], function () {
    updateOldPassword();
  })

  watch([confirm_password], function () {
      updateConfirmPassword()
      if (confirm_password.value != password.value) {
          new_password_state.value = false;
      } else {
          if (confirm_password.value == "") {
              new_password_state.value = null;
              return;
          }
          new_password_state.value = true;
      }
  })
  watch([password], function () {
      updatePassword();
      if (confirm_password.value != password.value) {
          new_password_state.value = false;
      } else {
          if (confirm_password.value == "") {
              new_password_state.value = null;
              return;
          }
          new_password_state.value = true;
      }
  })
  </script>

  <style>
  </style>
