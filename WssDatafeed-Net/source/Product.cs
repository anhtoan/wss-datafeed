using System;
using System.Xml.Serialization;

namespace WssDataFeed
{
    [Serializable]
    [XmlRoot("Product")]
    public class Product
    {
        [XmlIgnore]
        public string SimpleSku { get; set; }//NOT
        [XmlElement("simple_sku")]//SKU sản phẩm
        public System.Xml.XmlCDataSection SimpleSkuCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(SimpleSku);
            }
            set
            {
                SimpleSku = value.Value;
            }
        }
        [XmlIgnore]
        public string ParentSku { get; set; }//NOT
        [XmlElement("parent_sku")]//SKU sản phẩm cha
        public System.Xml.XmlCDataSection ParentSkuCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(ParentSku);
            }
            set
            {
                ParentSku = value.Value;
            }
        }
        [XmlIgnore]
        public string AvailabilityInstock { get; set; }
        [XmlElement("availability_instock")]//Còn hàng hay hết hàng (not null)
        public System.Xml.XmlCDataSection AvailabilityInstockCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(AvailabilityInstock);
            }
            set
            {
                AvailabilityInstock = value.Value;
            }
        }
        [XmlIgnore]
        public string Brand { get; set; }
        [XmlElement("brand")]//Tên hãng sản xuất
        public System.Xml.XmlCDataSection BrandCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Brand);
            }
            set
            {
                Brand = value.Value;
            }
        }
        [XmlIgnore]
        public string ProductName { get; set; }
        [XmlElement("product_name")]//Tên sản phẩm (not null)
        public System.Xml.XmlCDataSection ProductNameCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(ProductName);
            }
            set
            {
                ProductName = value.Value;
            }
        }
        [XmlIgnore]
        public string Description { get; set; }
        [XmlElement("description")]//Mô tả sản phẩm (not null) (~220 words, plain text)
        public System.Xml.XmlCDataSection DescriptionCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Description);
            }
            set
            {
                Description = value.Value;
            }
        }
        [XmlIgnore]
        public string Currency { get; set; }
        [XmlElement("currency")]//Tiền tệ (not null) (VND or USD)
        public System.Xml.XmlCDataSection CurrencyCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Currency);
            }
            set
            {
                Currency = value.Value;
            }
        }
        [XmlIgnore]
        public string Price { get; set; }
        [XmlElement("price")] //Giá sản phẩm (not null) (format theo định dạng xxx,xxx.xxx, VD: 180,000)
        public System.Xml.XmlCDataSection PriceCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Price);
            }
            set
            {
                Price = value.Value;
            }
        }
        [XmlIgnore]
        public string Discount { get; set; }//NOT
        [XmlElement("discount")]//Số tiền khuyến mãi (default = 0)
        public System.Xml.XmlCDataSection DiscountCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Discount);
            }
            set
            {
                Discount = value.Value;
            }
        }
        [XmlIgnore]
        public string DiscountedPrice { get; set; }
        [XmlElement("discounted_price")]//Giá khuyến mãi (default = Price)
        public System.Xml.XmlCDataSection DiscountedPriceCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(DiscountedPrice);
            }
            set
            {
                DiscountedPrice = value.Value;
            }
        }
        [XmlIgnore]
        public string ParentOfParentOfCat1 { get; set; }//NOT
        [XmlElement("parent_of_parent_of_cat1")]//Tên danh mục cha của cha 1
        public System.Xml.XmlCDataSection ParentOfParentOfCat1CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(ParentOfParentOfCat1);
            }
            set
            {
                ParentOfParentOfCat1 = value.Value;
            }
        }
        [XmlIgnore]
        public string ParentOfCat1 { get; set; }
        [XmlElement("parent_of_cat_1")]//Tên danh mục cha 1
        public System.Xml.XmlCDataSection ParentOfCat1CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(ParentOfCat1);
            }
            set
            {
                ParentOfCat1 = value.Value;
            }
        }
        [XmlIgnore]
        public string Category1 { get; set; }
        [XmlElement("category_1")]//Tên danh mục 1 (not null)
        public System.Xml.XmlCDataSection Category1CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Category1);
            }
            set
            {
                Category1 = value.Value;
            }
        }
        [XmlIgnore]
        public string ParentOfParentOfCat2 { get; set; }//NOT
        [XmlElement("parent_of_parent_of_cat2")]//Tên danh mục cha của cha 2
        public System.Xml.XmlCDataSection ParentOfParentOfCat2CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(ParentOfParentOfCat2);
            }
            set
            {
                ParentOfParentOfCat2 = value.Value;
            }
        }
        [XmlIgnore]
        public string ParentOfCat2 { get; set; }
        [XmlElement("parent_of_cat_2")]//Tên danh mục cha 2
        public System.Xml.XmlCDataSection ParentOfCat2CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(ParentOfCat2);
            }
            set
            {
                ParentOfCat2 = value.Value;
            }
        }
        [XmlIgnore]
        public string Category2 { get; set; }
        [XmlElement("category_2")]//Tên danh mục 2
        public System.Xml.XmlCDataSection Category2CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Category2);
            }
            set
            {
                Category2 = value.Value;
            }
        }
        [XmlIgnore]
        public string ParentOfParentOfCat3 { get; set; }//NOT
        [XmlElement("parent_of_parent_of_cat3")]//Tên danh mục cha của cha 3
        public System.Xml.XmlCDataSection ParentOfParentOfCat3CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(ParentOfParentOfCat3);
            }
            set
            {
                ParentOfParentOfCat3 = value.Value;
            }
        }
        [XmlIgnore]
        public string ParentOfCat3 { get; set; }
        [XmlElement("parent_of_cat3")]//Tên danh mục cha 3
        public System.Xml.XmlCDataSection ParentOfCat3CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(ParentOfCat3);
            }
            set
            {
                ParentOfCat3 = value.Value;
            }
        }
        [XmlIgnore]
        public string Category3 { get; set; }
        [XmlElement("category_3")]//Tên danh mục 3
        public System.Xml.XmlCDataSection Category3CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Category3);
            }
            set
            {
                Category3 = value.Value;
            }
        }
        [XmlIgnore]
        public string PictureUrl { get; set; }
        [XmlElement("picture_url")]//Anh đại diện của sản phẩm (not null)
        public System.Xml.XmlCDataSection PictureUrlCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(PictureUrl);
            }
            set
            {
                PictureUrl = value.Value;
            }
        }
        [XmlIgnore]
        public string PictureUrl2 { get; set; }
        [XmlElement("picture_url2")]//Anh liên quan, ảnh mô tả sản phẩm
        public System.Xml.XmlCDataSection PictureUrl2CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(PictureUrl2);
            }
            set
            {
                PictureUrl2 = value.Value;
            }
        }
        [XmlIgnore]
        public string PictureUrl3 { get; set; }
        [XmlElement("picture_url3")]
        public System.Xml.XmlCDataSection PictureUrl3CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(PictureUrl3);
            }
            set
            {
                PictureUrl3 = value.Value;
            }
        }
        [XmlIgnore]
        public string PictureUrl4 { get; set; }
        [XmlElement("picture_url4")]
        public System.Xml.XmlCDataSection PictureUrl4CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(PictureUrl4);
            }
            set
            {
                PictureUrl4 = value.Value;
            }
        }
        [XmlIgnore]
        public string PictureUrl5 { get; set; }
        [XmlElement("picture_url5")]
        public System.Xml.XmlCDataSection PictureUrl5CDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(PictureUrl5);
            }
            set
            {
                PictureUrl5 = value.Value;
            }
        }
        [XmlIgnore]
        public string URL { get; set; }//NOT
        [XmlElement("URL")]//Đường dẫn đến sản phẩm (not null)
        public System.Xml.XmlCDataSection URLCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(URL);
            }
            set
            {
                URL = value.Value;
            }
        }
        [XmlIgnore]
        public string Promotion { get; set; }
        [XmlElement("promotion")]//Thông tin khuyến mãi (plain text có dấu)
        public System.Xml.XmlCDataSection PromotionCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(Promotion);
            }
            set
            {
                Promotion = value.Value;
            }
        }
        [XmlIgnore]
        public string DeliveryPeriod { get; set; }//NOT
        [XmlElement("delivery_period")]//Thời gian giao hàng
        public System.Xml.XmlCDataSection DeliveryPeriodCDATA
        {
            get
            {
                return new System.Xml.XmlDocument().CreateCDataSection(DeliveryPeriod);
            }
            set
            {
                DeliveryPeriod = value.Value;
            }
        }
        public Product()
        {
            SimpleSku = "";
            ParentSku = "";
            AvailabilityInstock = "false";
            Brand = "";
            ProductName = "";
            Description = "";
            Currency = "";
            Price = "0";
            Discount = "0";
            DiscountedPrice = "0";
            ParentOfParentOfCat1 = "";
            ParentOfCat1 = "";
            Category1 = "";
            ParentOfParentOfCat2 = "";
            ParentOfCat2 = "";
            Category2 = "";
            ParentOfParentOfCat3 = "";
            ParentOfCat3 = "";
            Category3 = "";
            PictureUrl = "";
            PictureUrl2 = "";
            PictureUrl3 = "";
            PictureUrl4 = "";
            PictureUrl5 = "";
            URL = "";
            Promotion = "";
            DeliveryPeriod = "";
        }
    }
}
