import { Transition } from "@headlessui/react";
import { Head, router, useForm, usePage } from "@inertiajs/react";

import Modal from "@/Components/Modal";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import Pagination from "@/Components/Pagination";
import PrimaryButton from "@/Components/PrimaryButton";
import Authenticated from "@/Layouts/AuthenticatedLayout";
import {
  Contact,
  ContactFormData,
  PageProps,
  Pagination as PaginationType,
} from "@/types";
import { FormEvent } from "react";
import { PencilIcon, TrashIcon } from "@heroicons/react/24/solid";

export default function Index({ auth }: PageProps) {
  const contacts = usePage().props.contacts as PaginationType<Contact>;
  const showForm = (usePage().props?.showModalForm as boolean) ?? false;
  const contact = (usePage().props?.contact as Contact) ?? null;
  const {
    data,
    setData,
    post,
    put,
    delete: postDelete,
    errors,
    processing,
    recentlySuccessful,
  } = useForm<ContactFormData>(
    contact ?? {
      id: null,
      name: null,
      email: null,
      phone: null,
      document: null,
    }
  );

  const submit = (e: FormEvent) => {
    e.preventDefault();
    data.id
      ? put(route("contacts.update", { id: data.id }))
      : post(route("contacts.store"));
  };

  const closeModal = () => {
    router.visit(route("contacts.index"));
  };
  return (
    <Authenticated
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          Contacts
        </h2>
      }
    >
      <Head title="Contacts" />
      <div className="sm:py-4 sm:p-4 lg:py-8 lg:p-8 ">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="mx-auto sm:py-3 lg:py-4">
            <PrimaryButton
              onClick={() => router.visit(route("contacts.create"))}
              className="bg-green-500 hover:bg-green-700 focus:bg-green-700 active:bg-green-900"
            >
              Create
            </PrimaryButton>
          </div>
          <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6 bg-white border-b border-gray-200">
              <table className="table-fixed w-full">
                <thead>
                  <tr className="bg-gray-100">
                    <th className="px-4 py-2 w-20">ID</th>
                    <th className="px-4 py-2">Name</th>
                    <th className="px-4 py-2">Email</th>
                    <th className="px-4 py-2">Phone</th>
                    <th className="px-4 py-2">Document</th>
                    <th className="px-4 py-2">Spreadsheet</th>
                    <th className="px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {contacts.data.map(
                    ({ id, name, email, phone, document, spreadsheet }) => (
                      <tr key={id}>
                        <td className="border px-4 py-2">{id}</td>
                        <td className="border px-4 py-2">{name}</td>
                        <td className="border px-4 py-2">{email}</td>
                        <td className="border px-4 py-2">{phone}</td>
                        <td className="border px-4 py-2">{document}</td>
                        <td className="border px-4 py-2">
                          {spreadsheet?.path ?? "-"}
                        </td>
                        <td className="border px-4 py-2 items-center space-x-2">
                          <PrimaryButton
                            onClick={() =>
                              router.visit(route("contacts.edit", { id }))
                            }
                            className="bg-blue-500 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900"
                          >
                            <PencilIcon className="h-4 w-4" />
                          </PrimaryButton>
                          <PrimaryButton
                            onClick={() =>
                              postDelete(route("contacts.destroy", { id }))
                            }
                            className="bg-red-500 hover:bg-red-700 focus:bg-red-700 active:bg-red-900"
                          >
                            <TrashIcon className="h-4 w-4" />
                          </PrimaryButton>
                        </td>
                      </tr>
                    )
                  )}
                </tbody>
              </table>
              <Pagination links={contacts.links} />
            </div>
          </div>
        </div>
      </div>

      <Modal show={showForm} onClose={closeModal}>
        <form onSubmit={submit} className="m-6 p-4">
          <div className="flex items-center">
            <InputLabel htmlFor="name" value="Name" />
            <TextInput
              id="name"
              name="name"
              value={data.name ?? ""}
              className="m-2 block w-full"
              onChange={(e) => setData("name", e.target.value)}
              required
            />
            <InputError className="mt-2" message={errors.name} />
          </div>
          <div className="flex items-center">
            <InputLabel htmlFor="email" value="Email" />
            <TextInput
              id="email"
              name="email"
              value={data.email ?? ""}
              className="m-2 block w-full"
              onChange={(e) => setData("email", e.target.value)}
              required
            />
            <InputError className="mt-2" message={errors.email} />
          </div>
          <div className="flex items-center">
            <InputLabel htmlFor="phone" value="Phone" />
            <TextInput
              id="phone"
              name="phone"
              value={data.phone ?? ""}
              className="m-2 block w-full"
              onChange={(e) => setData("phone", e.target.value)}
            />
            <InputError className="mt-2" message={errors.phone} />
          </div>
          <div className="flex items-center">
            <InputLabel htmlFor="document" value="Document" />
            <TextInput
              id="document"
              name="document"
              value={data.document ?? ""}
              className="m-2 block w-full"
              onChange={(e) => setData("document", e.target.value)}
            />
            <InputError className="mt-2" message={errors.document} />
          </div>
          <div className="mt-4 flex items-center">
            <PrimaryButton disabled={processing}>
              {data.id ? "Update" : "Create"}
            </PrimaryButton>

            <Transition
              show={recentlySuccessful}
              enter="transition ease-in-out"
              enterFrom="opacity-0"
              leave="transition ease-in-out"
              leaveTo="opacity-0"
            >
              <p className="text-sm text-gray-600">Saved.</p>
            </Transition>
          </div>
        </form>
      </Modal>
    </Authenticated>
  );
}
