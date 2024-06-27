<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { reactive, ref, toRaw, watch, inject } from "vue";
import { debounce } from "lodash-es";
import { message } from "ant-design-vue";

const info = inject("info");
const emit = defineEmits(["employeeCreated"]);
const formRef = ref();
const visible = ref(false);
const formState = reactive({
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    company_id: "",
});

const onOk = () => {
    formRef.value
        .validateFields()
        .then(() => {
            formState.company_id = companies.value.value;
            axios
                .post("/employees", toRaw(formState))
                .then((response) => {
                    info("success", response.data.message);
                    visible.value = false;
                    formRef.value.resetFields();
                    emit("employeeCreated");
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

let lastFetchId = 0;
const companies = reactive({
    data: [],
    value: [],
    fetching: false,
});
const fetchUser = debounce((value) => {
    lastFetchId += 1;
    const fetchId = lastFetchId;
    companies.data = [];
    companies.fetching = true;
    fetch(`/companies?search=${value}`)
        .then((response) => response.json())
        .then((body) => {
            // console.log(body)
            if (fetchId !== lastFetchId) {
                // for fetch callback order
                return;
            }
            const data = body.data.map((user) => ({
                label: `${user.name}`,
                value: user.id,
            }));
            companies.data = data;
            companies.fetching = false;
        });
}, 300);
watch(companies.value, () => {
    companies.data = [];
    companies.fetching = false;
});
</script>

<template>
    <Head title="Employees"></Head>
    <a-button @click="visible = true" class="float-end"
            >Create Employee</a-button
        >
    <div>
        <a-modal
            v-model:open="visible"
            title="Create Employee"
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
                    name="first_name"
                    label="First Name"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
                >
                    <a-input v-model:value="formState.first_name" />
                </a-form-item>

                <a-form-item
                    name="last_name"
                    label="Last Name"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
                >
                    <a-input v-model:value="formState.last_name" />
                </a-form-item>

                <a-form-item name="company" label="Select Company">
                    <a-select
                        v-model:value="companies.value"
                        show-search
                        label-in-value
                        placeholder="Select users"
                        style="width: 100%"
                        :filter-option="false"
                        :not-found-content="
                            companies.fetching ? undefined : null
                        "
                        :options="companies.data"
                        @search="fetchUser"
                        size="large"
                    >
                        <template v-if="companies.fetching" #notFoundContent>
                            <a-spin size="small" />
                        </template>
                    </a-select>
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

                <a-form-item
                    name="phone"
                    label="Phone"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
                >
                    <a-input v-model:value="formState.phone" />
                </a-form-item>
            </a-form>
        </a-modal>
    </div>
</template>
<style scoped>
.collection-create-form_last-form-item {
    margin-bottom: 0;
}
.ant-btn-default{
    margin-top: -38px;
}
</style>
