<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { reactive, ref, toRaw, inject } from "vue";

const emit = defineEmits(["companyCreated"]);
const formRef = ref();
const visible = ref(false);
const fileList = ref([]);

const formState = reactive({
    name: "",
    email: "",
    website: "",
    file: "",
});
const beforeUpload = (file) => {
    formState.file = file;
    fileList.value = [file];
    return false; // Prevent automatic upload
};

const handleRemove = () => {
    formState.file = null;
    fileList.value = [];
};
const onOk = () => {
    formRef.value
        .validateFields()
        .then(() => {
            const formData = new FormData();
            formData.append("file", formState.file);
            formData.append("name", formState.name);
            formData.append("email", formState.email);
            formData.append("website", formState.website);
            axios
                .post("/companies", formData)
                .then((response) => {
                    info("success", response.data.message);
                    visible.value = false;
                    formRef.value.resetFields();
                    emit("companyCreated");
                })
                .catch((error) => {
                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.errors
                    ) {
                        const errors = error.response.data.errors;
                        const firstError = Object.values(errors)[0][0];
                        info("error", firstError);
                    } else {
                        info("error", "Something went wring");
                    }
                });
        })
        .catch((info) => {
            console.log("Validation Failed:", info);
        });
};
</script>

<template>
    <Head title="Dashboard" />

    <div>
        <a-button @click="visible = true" class="float-end"
            >Create Company</a-button
        >
        <div class="clear:both"></div>
        <a-modal
            v-model:open="visible"
            title="Create a new comapny"
            ok-text="Create"
            cancel-text="Cancel"
            @ok="onOk"
        >
            <a-form
                ref="formRef"
                :model="formState"
                layout="vertical"
                name="form_in_modal"
            >
                <a-form-item
                    name="name"
                    label="Name"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
                >
                    <a-input v-model:value="formState.name" />
                </a-form-item>

                <a-form-item
                    name="email"
                    label="Email"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
                >
                    <a-input v-model:value="formState.email" />
                </a-form-item>

                <a-form-item name="upload" label="Select Logo">
                    <a-upload
                        :file-list="fileList"
                        :before-upload="beforeUpload"
                        @remove="handleRemove"
                        :max-count="1"
                    >
                        <a-button> Select File </a-button>
                    </a-upload>
                </a-form-item>

                <a-form-item
                    name="website"
                    label="Website"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
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
.ant-btn-default {
    margin-top: -38px;
}
</style>
