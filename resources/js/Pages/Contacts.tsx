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
import { FormEvent, MouseEventHandler } from "react";
import { PencilIcon, TrashIcon } from "@heroicons/react/24/solid";

export default function Index({ auth }: PageProps) {
  const contacts = usePage().props.contacts as PaginationType<Contact>;
  const showForm = (usePage().props?.showModalForm as boolean) ?? false;
  const contact = (usePage().props?.contact as Contact) ?? null;
  // @ts-ignore
  const queryParams = usePage().props?.ziggy?.query ?? {};
  const [, sortDirection] =
    queryParams?.sort === undefined || typeof queryParams?.sort === "function"
      ? [, "asc"]
      : queryParams.sort.split("|");

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
      created_at: null,
      updated_at: null,
    }
  );

  const submit = (e: FormEvent) => {
    e.preventDefault();
    data.id
      ? put(route("contacts.update", { id: data.id }))
      : post(route("contacts.store"));
  };

  const sortBy = (column: string): MouseEventHandler => {
    return () => {
      queryParams.sort = `${column}|${
        sortDirection === "asc" ? "desc" : "asc"
      }`;
      router.get(route("contacts.index", [{}, queryParams]));
    };
  };

  const closeModal = () => {
    router.get(route("contacts.index", [{}, queryParams]));
  };
  return (
    <Authenticated user={auth.user}>
      <Head title="Contacts" />
      <div className="sm:py-4 sm:p-4 lg:py-8 lg:p-8 ">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="mx-auto sm:py-3 lg:py-4">
            <PrimaryButton
              onClick={() =>
                router.visit(route("contacts.create", [{}, queryParams]))
              }
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
                    <th
                      className="px-4 py-2 w-20 cursor-pointer"
                      onClick={sortBy("id")}
                    >
                      ID
                    </th>
                    <th
                      className="px-4 py-2 cursor-pointer"
                      onClick={sortBy("name")}
                    >
                      Name
                    </th>
                    <th
                      className="px-4 py-2 cursor-pointer"
                      onClick={sortBy("email")}
                    >
                      Email
                    </th>
                    <th className="px-4 py-2">Phone</th>
                    <th className="px-4 py-2">Document</th>
                    <th
                      className="px-4 py-2 cursor-pointer"
                      onClick={sortBy("created_at")}
                    >
                      Created
                    </th>
                    <th
                      className="px-4 py-2 cursor-pointer"
                      onClick={sortBy("updated_at")}
                    >
                      Updated
                    </th>
                    <th className="w-36 px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {contacts.data.map(
                    ({
                      id,
                      name,
                      email,
                      phone,
                      document,
                      created_at,
                      updated_at,
                    }) => (
                      <tr key={id}>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {id}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {name}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {email}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {phone}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {document}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {created_at}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {updated_at}
                        </td>
                        <td className="border px-4 py-2 space-x-2 text-center">
                          <PrimaryButton
                            onClick={() =>
                              router.get(
                                route("contacts.edit", [{ id: 1 }, queryParams])
                              )
                            }
                            className="bg-blue-500 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900"
                          >
                            <PencilIcon className="h-4 w-4" />
                          </PrimaryButton>
                          <PrimaryButton
                            onClick={() =>
                              postDelete(
                                route("contacts.destroy", [{ id }, queryParams])
                              )
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
