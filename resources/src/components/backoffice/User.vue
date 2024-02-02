<template>
  <div class="d-flex justify-content-between card user flex-row">
    <div>
      <div>
        <div class="first-name">{{ props.user.first_name }} {{ props.user.last_name }}</div>
        <div class="role">
          {{ t(props.user.role.name) }}
        </div>
      </div>
      <div class="email">{{ props.user.email }}</div>
    </div>
    <div class="user-card-action">
        <img class="pointer" @click="props.editClick" src="../../../images/icon-actions-ic-edit.svg">
        <img class="pointer" @click="props.deleteClick" src="../../../images/icon-actions-ic-delete.svg">
    </div>
  </div>

</template>

<script setup>
import { useI18n } from "vue-i18n";
const { t, locale } = useI18n();
const props = defineProps({
  user: Object,
  editClick: Function,
  deleteClick: Function
});
</script>

<style scoped>
.user {
  padding: 20px;
}

.user-card-action {
    height: 30px;
}

.user .role {
  color: #828da0;
  font-size: 12px;
}

.user .email {
  font-size: 12px;
}
</style>
