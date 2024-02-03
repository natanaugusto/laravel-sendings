import { describe, it, vitest, expect } from "vitest";
import { render } from "@testing-library/react";

import Index from "./Index";

type ZiggyRecordMock = Record<"url" | "path", { url: string; path: string }>;

describe("Input component", () => {
  it("renders with text input", () => {
    // Mock the spreadsheets data for testing
    const mockSpreadsheets = {
      data: [
        {
          id: 1,
          user: { name: "User1" },
          path: "Path1",
          rows: 10,
          imported: 5,
          fails: 2,
        },
        {
          id: 2,
          user: { name: "User2" },
          path: "Path2",
          rows: 15,
          imported: 10,
          fails: 3,
        },
        // Add more data as needed
      ],
      links: {
        // Mock the pagination links for testing
        // Adjust according to your actual data structure
      },
    };

    const mockUsePage = () => ({ props: { spreadsheets: mockSpreadsheets } });
    vitest.mock("@inertiajs/react", () => ({ usePage: mockUsePage }));
    const props = {
      auth: {
        user: {
          id: 1,
          name: "John Doe",
          email: "jhon@doe.com",
          email_verified_at: "never",
        },
      },
    };

    const { getByText } = render(<Index {...props} />);
    expect(getByText("Spreadsheets"));
  });
});
