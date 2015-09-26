using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Data.SqlClient;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace WssDataFeed
{
    public class ConnectDB
    {
        public static DataTable ExecuteQuery(string query)
        {
            //SELECT SP.[TINHTRANG],HA.[TENLOAI] AS TENHANG,SP.[TENSP],SP.[MOTA],SP.[DONVI],SP.[GIA],SP.[GIAKM],LO2.[TENLOAI] AS CATECHA,LO.[TENLOAI] AS CTAECON,SP.[HINHNHO],SP.[HINHLON],SP.[KHUYENMAI]
            //FROM [mykim_dtb2013].[dbo].[SANPHAM] AS SP LEFT JOIN [mykim_dtb2013].[dbo].[SANPHAM_HANG] AS HA ON SP.[IDH] = HA.[IDH]
            //LEFT JOIN [mykim_dtb2013].[dbo].[SANPHAM_LOAI] AS LO ON SP.[IDLOAI] = LO.[IDLOAI]
            //LEFT JOIN [mykim_dtb2013].[dbo].[SANPHAM_LOAI] AS LO2 ON LO.[IDCHA] = LO2.[IDLOAI]
            //WHERE SP.[HIEULUC]=1 AND HA.[HIEULUC]=1 AND LO.[HIEULUC]=1 AND SP.[GIA]>0
            DataTable dataTable = new DataTable();
            string connetionString = ConnectionString.MainConnection;
            SqlConnection connection = new SqlConnection(connetionString);
            //SqlDataReader dataReader;
            try
            {
                connection.Open();
                SqlCommand command = new SqlCommand(query, connection);
                //dataReader = command.ExecuteReader();
                new SqlDataAdapter(command).Fill(dataTable);
                //while (dataReader.Read())
                //{
                //}
                //dataReader.Close();
                command.Dispose();
                connection.Close();
            }
            catch (Exception ex)
            {
                return dataTable;
            }
            return dataTable;
        }
    }

    public class ConnectionString
    {
        public static String MainConnection
        {
            get { return ConfigurationManager.AppSettings["MainConnectionString"].ToString(); }
        }
    }
}
