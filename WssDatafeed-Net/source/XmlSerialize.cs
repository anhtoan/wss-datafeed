using System.Collections.Generic;
using System.IO;
using System.Text;
using System.Xml.Serialization;

namespace WssDataFeed
{
    public class XmlSerialize
    {
        public static string Serialize(List<Product> list)//, string filePath)
        {
            XmlSerializer serializer = new XmlSerializer(typeof(List<Product>), new XmlRootAttribute("Products"));
            //using (TextWriter writer = new StreamWriter(HttpContext.Current.Server.MapPath(filePath), false, System.Text.Encoding.UTF8))
            using (StringWriter writer = new StringWriterUtf8())
            {
                serializer.Serialize(writer, list);
                return writer.ToString();
            }
        }
    }
    public class StringWriterUtf8 : StringWriter
    {
        public override Encoding Encoding
        {
            get { return Encoding.UTF8; }
        }
    }
}
