using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using WssDataFeed;

namespace XmlDataFeed
{
    public partial class Default : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            if (!IsPostBack)
            {
                List<Product> Products = new List<Product>();

                Products = MappingData.GetProducts();
                //var storePath = "/WssDataFeed.txt";
                var xml = XmlSerialize.Serialize(Products);
                Response.Clear();
                Response.Buffer = true;
                Response.Charset = "";
                Response.Cache.SetCacheability(HttpCacheability.NoCache);
                Response.ContentType = "application/xml";
                Response.Write(xml);
                Response.Flush();
                Response.End();
            }
        }
    }
}