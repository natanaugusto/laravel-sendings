import FileInput from "@/Components/FileInput";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import Pagination from "@/Components/Pagination";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import Authenticated from "@/Layouts/AuthenticatedLayout";
import { PageProps, Pagination as PaginationType, Spreadsheet } from "@/types";
import { Transition } from "@headlessui/react";
import { Head, useForm, usePage } from "@inertiajs/react";

export default function Index({ auth }: PageProps) {
  const spreadsheets = usePage().props.spreadsheets as PaginationType<
    Spreadsheet
  >;
  const {
    data,
    setData,
    patch,
    errors,
    processing,
    recentlySuccessful,
  } = useForm({ file: null });
  const submit = () => {};
  return (
    <Authenticated
      user={auth.user}
      header={
        <h2 className="font-semibold text-xl text-gray-800 leading-tight">
          Spreadsheets
        </h2>
      }
    >
      <Head title="Spreadsheets" />
      <div className="sm:py-4 sm:pb-2 lg:py-8 lg:pb-4">
        <div className="max-w-7xl mx-auto sm:px-4 lg:px-6 space-y-6">
          <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <section className={"max-w-xl"}>
              <header>
                <h2 className="text-lg font-medium text-gray-900">
                  Spreadsheet upload
                </h2>

                <p className="mt-1 text-sm text-gray-600">
                  Upload a Spreadsheet file to be imported as Contacts
                </p>
              </header>

              <form onSubmit={submit} className="mt-6 space-y-6">
                <div>
                  <InputLabel htmlFor="file" value="File" />
                  <FileInput
                    id="file"
                    className="mt-1 block w-full"
                    onChange={submit}
                    required
                  />

                  <InputError className="mt-2" message={errors.file} />
                </div>
                <div className="flex items-center gap-4">
                  <PrimaryButton disabled={processing}>Upload</PrimaryButton>

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
            </section>
          </div>
        </div>
      </div>

      <div className="sm:py-4 sm:pt-2 lg:py-8 lg:pt-2 ">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6 bg-white border-b border-gray-200">
              <table className="table-fixed w-full">
                <thead>
                  <tr className="bg-gray-100">
                    <th className="px-4 py-2 w-20">ID</th>
                    <th className="px-4 py-2">User</th>
                    <th className="px-4 py-2">Path</th>
                    <th className="px-4 py-2">Rows</th>
                    <th className="px-4 py-2">Imported</th>
                    <th className="px-4 py-2">Fails</th>
                  </tr>
                </thead>
                <tbody>
                  {spreadsheets.data.map(
                    ({ id, user, path, rows, imported, fails }) => (
                      <tr>
                        <td className="border px-4 py-2">{id}</td>
                        <td className="border px-4 py-2">{user.name}</td>
                        <td className="border px-4 py-2">{path}</td>
                        <td className="border px-4 py-2">{rows}</td>
                        <td className="border px-4 py-2">{imported}</td>
                        <td className="border px-4 py-2">{fails}</td>
                      </tr>
                    )
                  )}
                </tbody>
              </table>
              <Pagination links={spreadsheets.links} />
            </div>
          </div>
        </div>
      </div>
    </Authenticated>
  );
}
