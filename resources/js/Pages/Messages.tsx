import { useRef } from "react";
import { Transition } from "@headlessui/react";
import { Head, router, useForm, usePage } from "@inertiajs/react";
import ReactQuill from "react-quill";
import "react-quill/dist/quill.snow.css";

import Modal from "@/Components/Modal";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import Pagination from "@/Components/Pagination";
import PrimaryButton from "@/Components/PrimaryButton";
import Authenticated from "@/Layouts/AuthenticatedLayout";
import {
  Message,
  MessageFormData,
  PageProps,
  Pagination as PaginationType,
} from "@/types";
import { FormEvent, MouseEventHandler } from "react";
import {
  PencilIcon,
  TrashIcon,
  PaperAirplaneIcon,
} from "@heroicons/react/24/solid";

export default function Index({ auth }: PageProps) {
  const messages = usePage().props.messages as PaginationType<Message>;
  const showForm = (usePage().props?.showModalForm as boolean) ?? false;
  const message = (usePage().props?.message as Message) ?? null;
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
  } = useForm<MessageFormData>(
    message ?? {
      id: null,
      user: null,
      subject: null,
      body: null,
      created_at: null,
      updated_at: null,
    }
  );

  const editor = useRef<any>(null);
  const quillModules = {
    toolbar: [
      ["bold", "italic", "underline", "strike"], // toggled buttons
      ["blockquote", "code-block"],
      ["link", "image"],
      [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
      [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
      [{ size: ["small", false, "large", "huge"] }], // custom dropdown
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ color: [] }, { background: [] }], // dropdown with defaults from theme
      [{ align: [] }],
      ["clean"], // remove formatting button
    ],
  };

  const submit = (e: FormEvent) => {
    e.preventDefault();
    data.id
      ? put(route("messages.update", { id: data.id }))
      : post(route("messages.store"));
  };

  const sortBy = (column: string): MouseEventHandler => {
    return () => {
      queryParams.sort = `${column}|${
        sortDirection === "asc" ? "desc" : "asc"
      }`;
      router.get(route("messages.index", [{}, queryParams]));
    };
  };

  const closeModal = () => {
    router.get(route("messages.index", [{}, queryParams]));
  };
  return (
    <Authenticated user={auth.user}>
      <Head title="Messages" />
      <div className="sm:py-4 sm:p-4 lg:py-8 lg:p-8 ">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="relative mx-auto sm:py-4 lg:py-6">
            <PrimaryButton
              onClick={() =>
                router.visit(route("messages.create", [{}, queryParams]))
              }
              className="absolute right-2 top-0 bg-green-500 hover:bg-green-700 focus:bg-green-700 active:bg-green-900"
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
                    <th className="px-4 py-2 cursor-pointer">User</th>
                    <th
                      className="px-4 py-2 cursor-pointer"
                      onClick={sortBy("subject")}
                    >
                      Subject
                    </th>
                    <th className="px-4 py-2 cursor-pointer">Body</th>
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
                    <th className="w-52 px-4 py-2">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {messages.data.map(
                    ({ id, user, subject, body, created_at, updated_at }) => (
                      <tr key={id}>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {id}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {user.name}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {subject}
                        </td>
                        <td className="border px-4 py-2 overflow-x-scroll scrollbar-hide text-center">
                          {body}
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
                              router.post(
                                route("messages.send", [{ id }, queryParams])
                              )
                            }
                            className="bg-green-500 hover:bg-green-700 focus:bg-green-700 active:bg-green-900"
                          >
                            <PaperAirplaneIcon className="h-4 w-4" />
                          </PrimaryButton>
                          <PrimaryButton
                            onClick={() =>
                              router.get(
                                route("messages.edit", [{ id }, queryParams])
                              )
                            }
                            className="bg-blue-500 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900"
                          >
                            <PencilIcon className="h-4 w-4" />
                          </PrimaryButton>
                          <PrimaryButton
                            onClick={() =>
                              postDelete(
                                route("messages.destroy", [{ id }, queryParams])
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
              <Pagination links={messages.links} />
            </div>
          </div>
        </div>
      </div>

      <Modal show={showForm} onClose={closeModal}>
        <form onSubmit={submit} className="m-6 p-4">
          <div className="flex items-center">
            <InputLabel htmlFor="subject" value="Subject" />
            <TextInput
              id="subject"
              name="subject"
              value={data.subject ?? ""}
              className="m-2 block w-full"
              onChange={(e) => setData("subject", e.target.value)}
              required
            />
            <InputError className="mt-2" message={errors.subject} />
          </div>
          <div className="flex min-h-80">
            <InputLabel htmlFor="body" value="Body" />
            <ReactQuill
              ref={editor}
              theme="snow"
              value={data.body}
              modules={{ ...quillModules }}
              onChange={(content) => setData("body", content)}
              className="m-2 mb-16 block w-full min-h-fit"
            />
            <InputError className="mt-2" message={errors.body} />
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
