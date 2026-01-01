import CustomizedTables from "./users/Users";
import axios from "axios";
import { useEffect, useState } from "react";

function App() {
  const [data, setData] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);
  //get users data from backend using axios
  useEffect(() => {
    async function fetchData() {
      try {
        const res = await axios.get(
          "http://localhost/archive-system/backend/users"
        );
        setData(res.data);
        setIsLoading(false);
      } catch (error) {
        console.log(error);
        setError(error);
        setIsLoading(false);
      }
    }
    fetchData();
  }, []);

  return (
    <>
      <div className="w-full flex justify-start items-center bg-amber-50 h-dvh flex-col py-12">
        {isLoading ? (
          <h1 className="font-bold text-5xl">Loading...</h1>
        ) : (
          <div>
            <h1 className="text-5xl py-5">Users Data</h1>
            <div className="">
              <CustomizedTables data={data} />
            </div>
          </div>
        )}
      </div>
    </>
  );
}

export default App;
