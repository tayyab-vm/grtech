<script setup>
const props = defineProps(["detailModalVisible", "company"]);

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

import { reactive, ref, toRaw, defineProps, watch } from "vue";

const emit = defineEmits(["handleModalClose", "companyCreated"]);

const formRef = ref();
const visible = ref(props.detailModalVisible);
const company = ref(props.company);

const formState = reactive({
    companyName: "",
    companyEmail: "",
    companyWebsite: "",
    companyLogo: "",
});

watch(
    () => props.detailModalVisible,
    (newValue) => {
        visible.value = newValue;
    }
);

watch(
    () => props.company,
    (newValue) => {
        formState.companyName = newValue.company.name;
        formState.companyEmail = newValue.company.email;
        formState.companyWebsite = newValue.company.website;
        formState.companyLogo = newValue.company.logo;
    }
);

const onOk = () => {
    formRef.value
        .validateFields()
        .then(() => {
            axios
                .put("/companies/" + formState.id, toRaw(formState))
                .then((response) => {
                    visible.value = false;
                    formRef.value.resetFields();
                    emit("companyCreated");
                    emit("handleModalClose");
                })
                .catch((error) => {
                    console.error("Error submitting form:", error);
                });
        })
        .catch((info) => {
            console.log("Validation Failed:", info);
        });
};
const closeModal = () => {
    visible.value = false;
    emit("handleModalClose", false);
};
</script>

<template>
    <Head title="Employees" ></Head>

    <div>
        <a-modal
            v-model:open="visible"
            title="Company Details"
            cancel-text="Ok"
            @ok="onOk"
            @cancel="closeModal"
        >
            <template #footer>
                <a-button key="back" @click="closeModal">Close</a-button>
            </template>
            <a-descriptions title="">
                <a-descriptions-item label="Name">{{
                    formState.companyName
                }}</a-descriptions-item>
            </a-descriptions>
            <a-descriptions title="">
                <a-descriptions-item label="Email">{{
                    formState.companyEmail
                }}</a-descriptions-item>
            </a-descriptions>
            <a-descriptions title="">
                <a-descriptions-item label="Website">{{
                    formState.companyWebsite
                }}</a-descriptions-item>
            </a-descriptions>
            <a-descriptions title="">
                <a-descriptions-item label="Logo">
                    <a-image :width="200" :src="`/storage/${formState.companyLogo}`" />
                </a-descriptions-item>
            </a-descriptions>
        </a-modal>
    </div>
</template>
<style scoped>
.collection-create-form_last-form-item {
    margin-bottom: 0;
}
</style>
