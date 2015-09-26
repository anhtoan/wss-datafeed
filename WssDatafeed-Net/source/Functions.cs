using System;
using System.Configuration;
using System.Globalization;
using System.Text.RegularExpressions;

namespace WssDataFeed
{
    public class Functions
    {
        static string extension = ".html";

        public static int Object2Integer(object value)
        {
            if (null == value) return int.MinValue;
            try
            {
                return Convert.ToInt32(value);
            }
            catch
            {
                return int.MinValue;
            }
        }
        /// <summary>
        /// Chuyển đổi 1 giá trị sang kiểu Long
        /// </summary>
        /// <param name="value">Giá trị cần chuyển đổi</param>
        /// <returns>Số kiểu Long, nếu lỗi return long.MinValue</returns>
        public static long Object2Long(object value)
        {
            if (null == value) return long.MinValue;
            try
            {
                return Convert.ToInt64(value);
            }
            catch
            {
                return long.MinValue;
            }
        }
        /// <summary>
        /// Chuyển đổi 1 giá trị sang kiểu Double
        /// </summary>
        /// <param name="value">Giá trị cần chuyển đổi</param>
        /// <returns>Số kiểu Double, nếu lỗi return double.NaN</returns>
        public static double Object2Double(object value)
        {
            if (null == value) return 0;
            try
            {
                return Convert.ToDouble(value);
            }
            catch
            {
                return 0;
            }
        }
        /// <summary>
        /// Chuyển đổi 1 giá trị sang kiểu float
        /// </summary>
        /// <param name="value">Giá trị cần chuyển đổi</param>
        /// <returns>Số kiểu float, nếu lỗi return float.NaN</returns>
        public static float Object2Float(object value)
        {
            if (null == value) return float.NaN;
            try
            {
                return float.Parse(value.ToString());
            }
            catch
            {
                return float.NaN;
            }
        }
        public static Decimal Object2Decimal(object value)
        {
            if (null == value) return Decimal.Zero;
            try
            {
                return Decimal.Parse(value.ToString());
            }
            catch
            {
                return Decimal.Zero;
            }
        }
        /// <summary>
        /// Chuyển đổi 1 giá trị sang kiểu boolean
        /// </summary>
        /// <param name="value">Giá trị cần chuyển đổi</param>
        /// <returns>giá trị kiểu boolean, nếu lỗi return false</returns>
        public static bool Object2Boolean(object value)
        {
            if (null == value) return false;
            try
            {
                return Convert.ToBoolean(value);
            }
            catch
            {
                return false;
            }
        }
        /// <summary>
        /// 
        /// </summary>
        /// <param name="Price"></param>
        /// <returns></returns>
        public static string FormatPriceVN(object Price)
        {
            CultureInfo ci = new CultureInfo("en-US"); // us
            long price = Object2Long(Price);
            return price.ToString("n0", ci);
        }
        /// <summary>
        /// 
        /// </summary>
        /// <param name="html"></param>
        /// <returns></returns>
        public static string HtmlToPlainText(string html)
        {
            const string tagWhiteSpace = @"(>|$)(\W|\n|\r)+<";//matches one or more (white space or line breaks) between '>' and '<'
            const string stripFormatting = @"<[^>]*(>|$)";//match any character between '<' and '>', even when end tag is missing
            const string lineBreak = @"<(br|BR)\s{0,1}\/{0,1}>";//matches: <br>,<br/>,<br />,<BR>,<BR/>,<BR />
            var lineBreakRegex = new Regex(lineBreak, RegexOptions.Multiline);
            var stripFormattingRegex = new Regex(stripFormatting, RegexOptions.Multiline);
            var tagWhiteSpaceRegex = new Regex(tagWhiteSpace, RegexOptions.Multiline);

            var text = html;
            //Decode html specific characters
            //text = System.Net.WebUtility.HtmlDecode(text);
            text = System.Web.HttpUtility.HtmlDecode(text);
            //Remove tag whitespace/line breaks
            text = tagWhiteSpaceRegex.Replace(text, "><");
            //Replace <br /> with line breaks
            text = lineBreakRegex.Replace(text, Environment.NewLine);
            //Strip formatting
            text = stripFormattingRegex.Replace(text, string.Empty);

            return text;
        }
        /// <summary>
        /// 
        /// </summary>
        /// <param name="HTMLText"></param>
        /// <returns></returns>
        public static string StripHTML(string HTMLText)
        {
            var reg = new Regex("<[^>]+>", RegexOptions.IgnoreCase);
            //return System.Net.WebUtility.HtmlDecode(reg.Replace(HTMLText, ""));
            return System.Web.HttpUtility.HtmlDecode(reg.Replace(HTMLText, ""));
        }

        public static string GetDetailProductUrl(string Name, long Id, int CateId, int ParentCateId)
        {
            var parentCate = ParentCateId > 0 ? ParentCateId.ToString() : "";
            var cate = CateId > 0 ? CateId.ToString() : "";
            return ConfigurationManager.AppSettings["Domain"] + "chi-tiet/" + UnicodeToKoDauAndGach(Name) + "-" + parentCate + "-" + cate + "-" + Id + extension;
        }


        #region Chuyen doi xau dang unicode co dau sang dang khong dau
        private const string KoDauChars =
            "aaaaaaaaaaaaaaaaaeeeeeeeeeeediiiiiooooooooooooooooouuuuuuuuuuuyyyyyAAAAAAAAAAAAAAAAAEEEEEEEEEEEDIIIOOOOOOOOOOOOOOOOOOOUUUUUUUUUUUYYYYYAADOOU";
        private const string uniChars =
            "àáảãạâầấẩẫậăằắẳẵặèéẻẽẹêềếểễệđìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵÀÁẢÃẠÂẦẤẨẪẬĂẰẮẲẴẶÈÉẺẼẸÊỀẾỂỄỆĐÌÍỈĨỊÒÓỎÕỌÔỒỐỔỖỘƠỜỚỞỠỢÙÚỦŨỤƯỪỨỬỮỰỲÝỶỸỴÂĂĐÔƠƯ";
        public static string UnicodeToKoDau(string s)
        {
            string retVal = String.Empty;
            s = s.Trim();
            int pos;
            for (int i = 0; i < s.Length; i++)
            {
                pos = uniChars.IndexOf(s[i].ToString());
                if (pos >= 0)
                    retVal += KoDauChars[pos];
                else
                    retVal += s[i];
            }
            return retVal;
        }
        public static string UnicodeToKoDauAndGach(string s)
        {
            string strChar = "-abcdefghijklmnopqrstxyzuvxw0123456789 ";
            //string retVal = UnicodeToKoDau(s);
            //s = s.Replace("-", " ");
            //s = s.Replace("–", "");
            s = s.Replace("  ", " ");
            //s = s.Replace("  ", " ");
            s = s.Replace("+", "-");
            s = UnicodeToKoDau(s.ToLower().Trim());
            string sReturn = "";
            for (int i = 0; i < s.Length; i++)
            {
                if (strChar.IndexOf(s[i]) > -1)
                {
                    if (s[i] != ' ')
                        sReturn += s[i];
                    else if (i > 0 && s[i - 1] != '-')
                        sReturn += "-";
                }
            }
            sReturn = sReturn.Replace("--", "-");
            return sReturn;
        }
        #endregion
    }
}
