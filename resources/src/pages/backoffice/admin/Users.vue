<template>
  <GenericModal
    key="confirm_delete_user_modal"
    id="confirm_delete_user_modal"
    :ok="(event) => deleteUser(event, $bvModal)"
    :title="t('delete_user')"
    :body="t('backoffice.delete_user_confirm_question')"
  ></GenericModal>
  <EditUser
    :key="edit_user.id"
    :edit_user="edit_user"
    v-on:edit-user-update-first-name="editUserUpdateFirstName"
    v-on:edit-user-update-last-name="editUserUpdateLastName"
    v-on:edit-user-update-email="editUserUpdateEmail"
    v-on:edit-user-update-role="editUserUpdateRole"
    :ok="(event) => editUser(event, $bvModal)"
    :open-change-password-modal="openEditUserChangePasswordModal"
  ></EditUser>
  <EditUser
    id="create-user-modal"
    :edit_user="create_user"
    :key="JSON.stringify(toRaw(create_user))"
    v-on:edit-user-update-first-name="createUserUpdateFirstName"
    v-on:edit-user-update-last-name="createUserUpdateLastName"
    v-on:edit-user-update-email="createUserUpdateEmail"
    v-on:edit-user-update-role="createUserUpdateRole"
    :ok="(event) => createUser(event, $bvModal)"
    :open-change-password-modal="openCreateUserChangePasswordModal"
    :title="t('create_user')"
  ></EditUser>
  <ChangePassword
    ref="changePasswordModal"
    :ok="(event) => editUserPassword(event, $bvModal)"
    :hide-old-password="true"
    v-on:update-password="editUserUpdatePassword"
    v-on:update-confirm-password="editUserUpdateConfirmPassword"
  />
  <ChangePassword
    ref="createChangePasswordModal"
    id="create-user-change-password-modal"
    :ok="(event) => console.log('ok')"
    :hide-old-password="true"
    v-on:update-password="createUserUpdatePassword"
    v-on:update-confirm-password="createUserUpdateConfirmPassword"
  />
  <div class="d-flex justify-content-between filter-header" ref="usersFilter">
    <div>
        <b-button class="m-1" variant="dark" @click="openCreateModal">{{ t('create_user') }}</b-button>
    </div>
    <div>
      <div class="d-flex">
        <b-form-input class="m-1" id="full_name" v-model="full_name_filter" :placeholder="t('full_name')"></b-form-input>
        <b-form-input class="m-1" id="email_filter" v-model="email_filter" :placeholder="t('email')"></b-form-input>
      </div>
    </div>
  </div>
  <div ref="usersList" class="infinite-scroll users-list">
    <User v-for="user in users" :user="user" :key="user.id" :delete-click="() => openConfirmDeleteUserModal(user)" :edit-click="() => openEditModalUser(user)" ></User>
  </div>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { ref, onMounted, watch, toRaw, reactive } from "vue";
import GenericModal from "../../../modals/GenericModal.vue";
import EditUser from "../../../modals/backoffice/EditUser.vue";
import ChangePassword from "../../../modals/backoffice/ChangePassword.vue";
import { createUserAPI, deleteUserAPI, editUserAPI, editUserPasswordAPI, getUsersAPI } from "../../../../js/utilities/api";
import Utils from "../../../../js/utilities/utils";
import User from "../../../components/backoffice/User.vue";
import { useModal } from "bootstrap-vue-next";
import * as db from 'lodash/debounce';
const { t, locale } = useI18n();
const props = defineProps({
  refreshUser: Function,
});
const edit_user = ref({});
const create_user = ref({});
const refreshUser = props.refreshUser;
const usersList = ref(null);
const usersFilter = ref(null);
const user = ref({});
const users = ref([]);
const page = ref(0);
const limit = ref(100);
const email_filter = ref("");
const full_name_filter = ref("");
const data_finished = ref(false);
const fetching_users_data = ref(false);
const fetching_user_data_timeout_id = ref(null)

// Valori della modale di modifica utente
const edit_user_first_name = ref('')
const edit_user_last_name = ref('')
const edit_user_email = ref('')
const edit_user_role = ref('')
const edit_user_password = ref('')
const edit_user_confirm_password = ref('')

const create_user_first_name = ref('')
const create_user_last_name = ref('')
const create_user_email = ref('')
const create_user_role = ref('')
const create_user_password = ref('')
const create_user_confirm_password = ref('')

const { show, hide, modal } = useModal('edit-user-modal')
const { show: showCreateModal, hide: hideCreateModal} = useModal('create-user-modal');
const { show: showConfirmDeleteUserModal, hide: hideConfirmDeleteUserModal} = useModal('confirm_delete_user_modal');
const { show: showEditUserPasswordModal, hide: hideEditUserPasswordModal} = useModal('change-password-modal');
const { show: showCreateUserPasswordModal, hide: hideCreateUserPasswordModal} = useModal('create-user-change-password-modal');
const openEditModalUser = (user) => {
    show();
    edit_user.value = user;
}

const openConfirmDeleteUserModal = (user) => {
    showConfirmDeleteUserModal();
    edit_user.value = user;
}

const openCreateUserChangePasswordModal = () => {
    showCreateUserPasswordModal();
}

const openEditUserChangePasswordModal = () => {
    showEditUserPasswordModal();
}

const openCreateModal = function () {
    showCreateModal();
}

const editUserPassword = () => {
    const data = {
        user_id: edit_user.value.id,
        password: edit_user_password.value,
        confirm_password: edit_user_confirm_password.value
    }

    editUserPasswordAPI(data, resp => {
        Utils.response.handleError(resp);
        Utils.DOM.toast('edit_password_successful', 'success', t);
    }, err => {
        Utils.DOM.toast(err.message, 'error', t);
        console.log(err.stack);
    })
}

const editUserUpdatePassword = (newValue) => {
    edit_user_password.value = newValue;
}

const editUserUpdateConfirmPassword = (newValue) => {
    edit_user_confirm_password.value = newValue;
}

const editUserUpdateFirstName = (newValue) => {
    edit_user_first_name.value = newValue;
}

const editUserUpdateLastName = (newValue) => {
    edit_user_last_name.value = newValue;
}

const editUserUpdateEmail = (newValue) => {
    edit_user_email.value = newValue;
}

const editUserUpdateRole = (newValue) => {
    edit_user_role.value = newValue;
}

const createUserUpdatePassword = (newValue) => {
    create_user_password.value = newValue;
    create_user.value.password = newValue;
}

const createUserUpdateConfirmPassword = (newValue) => {
    create_user_confirm_password.value = newValue;
    create_user.value.confirm_password = newValue;
}

const createUserUpdateFirstName = (newValue) => {
    create_user_first_name.value = newValue;
    create_user.value.first_name = newValue;

}

const createUserUpdateLastName = (newValue) => {
    create_user_last_name.value = newValue;
    create_user.value.last_name = newValue;
}

const createUserUpdateEmail = (newValue) => {
    create_user_email.value = newValue;
    create_user.value.email = newValue;
}

const createUserUpdateRole = (newValue) => {
    create_user_role.value = newValue;
    create_user.value.role_id = newValue;
}

const createUser = (e) => {
    e.preventDefault();

    if (
        (
            !create_user_password.value || !create_user_confirm_password.value
        )
    ) {
        Utils.DOM.toast('fill_password','error', t);
        return;
    }

    const data = {
        first_name: create_user_first_name.value,
        last_name: create_user_last_name.value,
        email: create_user_email.value,
        role_id: create_user_role.value,
        password: create_user_password.value,
        confirm_password: create_user_confirm_password.value
    }

    createUserAPI(data, resp => {
        Utils.response.handleError(resp);
        hideCreateModal();
        resetData();
        getUsers();
    }, err => {
        Utils.DOM.toast(err.message);
        console.log(err.stack);
    })
}

const deleteUser = (e) => {
  e.preventDefault();
  const data = {
    user_id: edit_user.value.id
  }

  deleteUserAPI(data, resp => {
    Utils.response.handleError(resp);
    updateUserInsideList(resp, true);
    hideConfirmDeleteUserModal();
  }, err => {
    Utils.DOM.toast(err.message);
    console.log(err.stack);
  })
}

const editUser = (e) => {
    const data = {
        first_name: edit_user_first_name.value,
        last_name: edit_user_last_name.value,
        email: edit_user_email.value,
        role_id: edit_user_role.value,
        user_id: edit_user.value.id
    }

    editUserAPI(data, resp => {
        Utils.response.handleError(resp);
        if (resp.id === user.value.id) {
            refreshUser(resp, emit, user, {})
        }
        updateUserInsideList(resp);
    }, err => {
        Utils.DOM.toast(err.message);
        console.log(err.stack);
    })
}

const updateUserInsideList = (editedUser, remove = false) => {
    const _users = users.value;
    let index = -1;
    _users.forEach((u, i) => {
        if (u.id == editedUser.id) {
            _users[i] = editedUser;
            index = i;
        }
    })
    if (remove) {
        _users.splice(index, 1);
    }
    users.value = _users;
}

const emit = defineEmits(["update-user"]);
refreshUser(null, emit, user, {});

const resetData = () => {
  users.value = [];
  page.value = 0;
};

const getUsers = () => {
  fetching_users_data.value = true;
  const data = {
    page: page.value,
    limit: limit.value,
    email: email_filter.value,
    full_name: full_name_filter.value
  };

  getUsersAPI(
    data,
    (resp) => {
      fetching_users_data.value = false;
      Utils.response.handleError(resp);
      const val = users.value;
      val.push(...resp);
      users.value = val;
      if (resp.length === 0) {
        data_finished.value = true;
      }
    },
    (err) => {
      fetching_users_data.value = false;
      Utils.DOM.toast(err.message, "error", t);
      console.log(err.stack);
    }
  );
};

const debouncedFilterRequest = reactive({
    search: db((term) => {
        // email_filter.value = term;
        resetData();
        getUsers()
    }, 500),
})

watch([full_name_filter], () => {
    debouncedFilterRequest.search(full_name_filter.value)
})

watch([email_filter], () => {
    debouncedFilterRequest.search(email_filter.value)
})

onMounted(() => {

    // usersFilter.value.onkeyup = (event) => {
    //     if (email_filter.value == event.target.value) {
    //         return;
    //     }
    //     debouncedFilterRequest.search(event.target.value);
    // }
  Utils.functions.initInfiniteScroll(usersList.value, () => {
    if (data_finished.value == false && !fetching_users_data.value) {
      page.value += 1;
      getUsers();
    }
  });
});

getUsers();
</script>

<style scoped>

.users-list {
  max-height: calc(100vh - 120px); /* -40px -40px -40px */
  overflow: auto;
  padding: 20px;
}
</style>
