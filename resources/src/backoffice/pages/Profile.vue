<template>
  <b-form @submit.prevent="editUser" id="user_data">
    <form-group-input
      id="first_name"
      type="text"
      :label="t('first_name')"
      :description="t('backoffice.input.first_name.description')"
      :validFeedback="t('backoffice.input.first_name.valid_feedback')"
      :invalidFeedback="t('backoffice.input.first_name.invalid_feedback')"
      :inputValue="first_name_value"
      v-model="first_name_value"
    >
    </form-group-input>
    <form-group-input
      id="last_name"
      type="text"
      :label="t('last_name')"
      :description="t('backoffice.input.last_name.description')"
      :validFeedback="t('backoffice.input.last_name.valid_feedback')"
      :invalidFeedback="t('backoffice.input.last_name.invalid_feedback')"
      :inputValue="last_name_value"
      v-model="last_name_value"
    >
    </form-group-input>
    <form-group-input
      id="email"
      type="text"
      :label="t('email')"
      :description="t('backoffice.input.email.description')"
      :validFeedback="t('backoffice.input.email.valid_feedback')"
      :invalidFeedback="t('backoffice.input.email.invalid_feedback')"
      :inputValue="email_value"
      v-model="email_value"
    >
    </form-group-input>
    <BButton class="m-1" type="submit" variant="dark">{{ t("edit") }}</BButton>
    <BButton class="m-1" variant="dark" v-b-modal.change-password-modal>{{
      t("edit_password")
    }}</BButton>
  </b-form>
  <ChangePassword
    ref="changePasswordModal"
    :ok="(event) => changePassword(event, $bvModal)"
    v-on:update-old-password="updateOldPassword"
    v-on:update-password="updatePassword"
    v-on:update-confirm-password="updateConfirmPassword"
  />
</template>

<script setup>
import { ref, watch, toRaw, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useModal } from "bootstrap-vue-next";
import ChangePassword from "../../components/backoffice/modals/ChangePassword.vue";
import {
  editUserAPI,
  editUserPasswordAPI,
} from "./../../../js/utilities/api.js";
import Utils from "./../../../js/utilities/utils.js";
const { t, locale } = useI18n();
const props = defineProps({
  refreshUser: Function,
});
import FormGroupInput from "../../components/FormGroupInput.vue";
const first_name_value = ref("");
const last_name_value = ref("");
const email_value = ref("");
const password = ref("");
const confirm_password = ref("");
const old_password = ref("");
const user = ref({});
const new_password_state = ref(null);
const changePasswordModal = ref();
const emit = defineEmits(["update-user"]);
const refreshUser = props.refreshUser;
console.log(refreshUser);

watch([confirm_password], function () {
  if (confirm_password.value != password.value) {
    new_password_state.value = false;
  } else {
    if (confirm_password.value == "") {
      new_password_state.value = null;
      return;
    }
    new_password_state.value = true;
  }
});
watch([password], function () {
  if (confirm_password.value != password.value) {
    new_password_state.value = false;
  } else {
    if (confirm_password.value == "") {
      new_password_state.value = null;
      return;
    }
    new_password_state.value = true;
  }
});



const { show, hide, modal } = useModal("change-password-modal");

const editUser = () => {
  const data = {
    first_name: first_name_value.value,
    last_name: last_name_value.value,
    email: email_value.value,
  };
  editUserAPI(
    data,
    (resp) => {
      Utils.response.handleError(resp);
      Utils.DOM.toast("backoffice.edit_user_successful", "success", t);
      refreshUser(resp, emit, user, {
        first_name: first_name_value,
        last_name: last_name_value,
        email: email_value,
      });
    },
    (err) => {
      Utils.DOM.toast(err.message, "error", t);
      console.log(err.stack);
    }
  );
};

const updateOldPassword = (newValue) => {
  old_password.value = newValue;
};
const updatePassword = (newValue) => {
  password.value = newValue;
};
const updateConfirmPassword = (newValue) => {
  confirm_password.value = newValue;
};

const changePassword = (event, modal) => {
  event.preventDefault();
  const data = {
    old_password: old_password.value,
    password: password.value,
    confirm_password: confirm_password.value,
  };
  editUserPasswordAPI(
    data,
    (resp) => {
      Utils.response.handleError(resp);
      hide();
      Utils.DOM.toast("backoffice.edit_password_successful", "success", t);
    },
    (err) => {
      Utils.DOM.toast(err.message, "error", t);
      console.log(err.stack);
    }
  );
};

onMounted(() => {
    refreshUser(null, emit, user, {
  first_name: first_name_value,
  last_name: last_name_value,
  email: email_value,
});
console.log("userinside", user);
})
</script>

<style>
</style>
