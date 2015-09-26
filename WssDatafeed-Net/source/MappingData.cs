using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace WssDataFeed
{
    public class MappingData
    {
        public static string ImgStore = ConfigurationManager.AppSettings["ImageStore"].ToString();
        /// <summary>
        /// get list product from DB
        /// </summary>
        /// <returns></returns>
        public static DataTable GetProductsFromDb()
        {
            DataTable data = new DataTable();
            var query =
                "SELECT SP.[TINHTRANG],HA.[TENLOAI] AS TENHANG,SP.[TENSP],SP.[MOTA],SP.[DONVI],SP.[GIA],SP.[GIAKM],LO2.[TENLOAI] AS CATECHA,LO.[TENLOAI] AS CTAECON,SP.[HINHNHO],SP.[HINHLON],SP.[KHUYENMAI],SP.[IDSP],LO.[IDLOAI] AS IDCATECON, LO2.[IDLOAI] AS IDCATECHA " +
                "FROM [mykim_dtb2013].[dbo].[SANPHAM] AS SP LEFT JOIN [mykim_dtb2013].[dbo].[SANPHAM_HANG] AS HA ON SP.[IDH] = HA.[IDH]" +
                "LEFT JOIN [mykim_dtb2013].[dbo].[SANPHAM_LOAI] AS LO ON SP.[IDLOAI] = LO.[IDLOAI]" +
                "LEFT JOIN [mykim_dtb2013].[dbo].[SANPHAM_LOAI] AS LO2 ON LO.[IDCHA] = LO2.[IDLOAI]" +
                "WHERE SP.[HIEULUC]=1 AND HA.[HIEULUC]=1 AND LO.[HIEULUC]=1 AND SP.[GIA]>0";
            try
            {
                data = ConnectDB.ExecuteQuery(query);
            }
            catch (Exception)
            {
            }
            return data;
        }
        /// <summary>
        /// mapping product from data row
        /// </summary>
        /// <param name="row"></param>
        /// <returns></returns>
        public static Product MapProduct(DataRow row)
        {
            Product product = new Product();
            product.AvailabilityInstock = (row["TINHTRANG"] != DBNull.Value && Functions.Object2Boolean(row["TINHTRANG"])).ToString();
            product.Brand = row["TENHANG"] != DBNull.Value ? row["TENHANG"].ToString() : string.Empty;
            product.ProductName = row["TENSP"] != DBNull.Value ? row["TENSP"].ToString() : string.Empty;
            product.Description = row["MOTA"] != DBNull.Value ? Functions.StripHTML(row["MOTA"].ToString()).Trim() : string.Empty;
            product.Currency = row["DONVI"] != DBNull.Value ? row["DONVI"].ToString() : string.Empty;
            var oldPrice = row["GIA"] != DBNull.Value ? Functions.Object2Long(row["GIA"]) : 0;
            var newPrice = row["GIAKM"] != DBNull.Value ? Functions.Object2Long(row["GIAKM"]) : 0;
            newPrice = newPrice > 0 ? newPrice : oldPrice;
            product.Price = Functions.FormatPriceVN(oldPrice);
            product.Discount = Functions.FormatPriceVN(oldPrice - newPrice);
            product.DiscountedPrice = Functions.FormatPriceVN(newPrice);
            product.ParentOfCat1 = row["CATECHA"] != DBNull.Value ? row["CATECHA"].ToString() : string.Empty;
            product.Category1 = row["CTAECON"] != DBNull.Value ? row["CTAECON"].ToString() : string.Empty;
            product.PictureUrl = row["HINHNHO"] != DBNull.Value ? (ImgStore + row["HINHNHO"].ToString()).Replace("../", "") : string.Empty;
            product.PictureUrl2 = row["HINHLON"] != DBNull.Value ? (ImgStore + row["HINHLON"].ToString()).Replace("../", "") : string.Empty;
            product.Promotion = row["KHUYENMAI"] != DBNull.Value ? Functions.StripHTML(row["KHUYENMAI"].ToString()).Trim() : string.Empty;
            var idSp = row["IDSP"] != DBNull.Value ? Functions.Object2Long(row["IDSP"]) : 0;
            var idCateCon = row["IDCATECON"] != DBNull.Value ? Functions.Object2Integer(row["IDCATECON"]) : 0;
            var idCateCha = row["IDCATECHA"] != DBNull.Value ? Functions.Object2Integer(row["IDCATECHA"]) : 0;
            product.URL = Functions.GetDetailProductUrl(product.ProductName, idSp, idCateCon, idCateCha);
            return product;
        }
        /// <summary>
        /// get all products
        /// </summary>
        /// <returns></returns>
        public static List<Product> GetProducts()
        {
            List<Product> products = new List<Product>();
            var data = GetProductsFromDb();
            if (data != null)
            {
                foreach (DataRow row in data.Rows)
                {
                    products.Add(MapProduct(row));
                }
            }
            return products;
        }
    }
}
