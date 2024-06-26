<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { reactive, ref, toRaw } from 'vue';

const emit = defineEmits(['companyCreated']);
const formRef = ref();
const visible = ref(false);
const formState = reactive({
    name: '',
    email: '',
    website: '',
  modifier: 'public',
});
const onOk = () => {
    formRef.value
        .validateFields()
        .then(() => {
            axios.post('/api/companies', toRaw(formState))
                .then(response => {
                    console.log('Response from API:', response.data);
                    visible.value = false;
                    formRef.value.resetFields();
                    emit('companyCreated');
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                });
        })
        .catch(info => {
            console.log('Validation Failed:', info);
        });
};
</script>

<template>
    <Head title="Dashboard" />

       

         <div>
        <a-button @click="visible = true" class="float-end">Create Company</a-button>
        <div class="clear:both"></div>
    <a-modal
      v-model:open="visible"
      title="Create a new comapny"
      ok-text="Create"
      cancel-text="Cancel"
      @ok="onOk"
    >
      <a-form ref="formRef" :model="formState" layout="vertical" name="form_in_modal">
        <a-form-item
          name="name"
          label="Name"
          :rules="[{ required: true, message: 'Please input the title of collection!' }]"
        >
          <a-input v-model:value="formState.name" />
        </a-form-item>

          <a-form-item
          name="email"
          label="Email"
          :rules="[{ required: true, message: 'Please input the title of collection!' }]"
        >
          <a-input v-model:value="formState.email" />
        </a-form-item>

        <a-form-item
        name="website"
        label="Website"
        :rules="[{ required: true, message: 'Please input the title of collection!' }]"
      >
        <a-input v-model:value="formState.website" />
      </a-form-item>
  
      </a-form>
    </a-modal>
  </div>
</template>
<style scoped>
.collection-create-form_last-form-item {
  margin-bottom: 0;
}
</style>