<template>
  <b-modal
    :id="props.id || 'edit-user-modal'"
    ok-variant="dark"
    @ok="props.ok"
    scrollable
    :title="props.title || t('edit_user')"
  >
    <b-form :id="(props.id || 'edit-user-modal') + 'user_data'">
      <form-group-input
        :id="(props.id || 'edit-user-modal') + '-edit_user_first_name'"
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
        :id="(props.id || 'edit-user-modal') + '-edit_user_last_name'"
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
        :id="(props.id || 'edit-user-modal') + '-edit_user_email'"
        type="text"
        :label="t('email')"
        :description="t('backoffice.input.email.description')"
        :validFeedback="t('backoffice.input.email.valid_feedback')"
        :invalidFeedback="t('backoffice.input.email.invalid_feedback')"
        :inputValue="email_value"
        v-model="email_value"
      >
      </form-group-input>
      <form-group-select
        :options="options"
        :id="(props.id || 'edit-user-modal') + '-edit_user_role'"
        v-model="role_value"
        :inputValue="role_value"
        :key="'role_' + role_value"
        :label="t('role')"
        :description="t('backoffice.input.role.description')"
        :validFeedback="t('backoffice.input.role.valid_feedback')"
        :invalidFeedback="t('backoffice.input.role.invalid_feedback')"
        :v-on:update:modelValue="updateRole()"
        ></form-group-select>
        <BButton class="m-1" variant="dark" @click="props.openChangePasswordModal">{{
          t("edit_password")
      }}</BButton>
    </b-form>
  </b-modal>
</template>

  <script setup>
import { onMounted, ref, watch, defineModel } from "vue";
import { useI18n } from "vue-i18n";
import Utils from "../../../js/utilities/utils.js";
const { t, locale } = useI18n();
import FormGroupInput from "../../components/FormGroupInput.vue";
import FormGroupSelect from "../../components/FormGroupSelect.vue";
import { getRolesAPI } from "../../../js/utilities/api.js";
const first_name_value = ref("");
const last_name_value = ref("");
const email_value = ref("");
const role_value = ref("");
const options = ref([]);
const emit = defineEmits([
  "edit-user-update-first-name",
  "edit-user-update-last-name",
  "edit-user-update-email",
  "edit-user-update-role",
]);

console.log('locale', locale)

const props = defineProps({
  ok: Function,
  openChangePasswordModal: Function,
  edit_user: {
    type: Object,
    default: {}
  },
  id: String,
  title: String
});


// const edit_user = defineModel('edit_user');

const updateRolesOption = () => {
    getRolesAPI({}, resp => {
        Utils.response.handleError(resp);
        if (resp.length) {
            const _options = [];
            resp.forEach(val => {
                let selected = null;
                if (props.edit_user.role_id == val.id) {
                    selected = true;
                }
                _options.push({
                    value: val.id,
                    text: t(val.name),
                    selected
                })
            })
            console.log(role_value);
            options.value = _options;
            role_value.value = props.edit_user.role_id;
        }
    }, err => {
        Utils.DOM.toast(err.message, 'error', t);
        console.log(err.stack);
    })
}

onMounted(() => {
    first_name_value.value = props.edit_user.first_name;
    last_name_value.value = props.edit_user.last_name;
    email_value.value = props.edit_user.email;
    console.log(role_value.value, email_value.value);
    console.log(props.edit_user);
    updateRolesOption();
})

const updateFirstName = () => {
  console.log("edit-user-update-first-name", first_name_value.value);
  emit("edit-user-update-first-name", first_name_value.value);
};
const updateLastName = () => {
  console.log("edit-user-update-last-name", last_name_value.value);
  emit("edit-user-update-last-name", last_name_value.value);
};
const updateEmail = () => {
  console.log("edit-user-update-email", email_value.value);
  emit("edit-user-update-email", email_value.value);
};
const updateRole = () => {
  console.log("edit-user-update-role", role_value.value);
  emit("edit-user-update-role", role_value.value);
};

watch([locale], function () {
    updateRolesOption();
})

watch([first_name_value], function () {
  updateFirstName();
});

watch([last_name_value], function () {
  updateLastName();
});

watch([email_value], function () {
  updateEmail()
});

// watch([role_value], function () {
//     console.log('role_value', role_value);
//     updateRole();
// })
</script>

  <style>
</style>
